<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Models\Asset;
use App\Models\Depreciation;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Throwable;

class RunBulkDepreciation implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    public $tries = 3;
    public $timeout = 1200;

    protected $companyId;
    protected $jobId;

    protected $assetsNotDepLock = null;
    protected $mainLock = null;

    public function __construct($companyId)
    {
        $this->companyId = $companyId;
        $this->jobId = 'depreciation_status_' . $this->companyId;
    }

    public function handle(): void
    {
        $mainLockKey = 'running-depreciation-process:' . $this->companyId;
        $this->mainLock = Cache::lock($mainLockKey, 1200);

        if (! $this->mainLock->block(2)) { // Tunggu 2 detik, kalau masih dikunci, release job
            Log::warning("Could not acquire lock for Company ID: {$this->companyId}; releasing job to retry later.");
            $this->release(30); // Coba lagi 30 detik kemudian
            return;
        }

        $lastMonth = Carbon::now()->subMonthNoOverflow();
        $lmYear = $lastMonth->year;
        $lmMonth = $lastMonth->month;
        $assetsLockKey = "lock_assets_not_depreciated_{$this->companyId}_{$lmYear}-{$lmMonth}";

        $this->assetsNotDepLock = Cache::lock($assetsLockKey, 600);
        if (! $this->assetsNotDepLock->block(2)) {
            Log::warning("Warning: could not acquire assets-not-depreciated lock ({$assetsLockKey}) for company {$this->companyId}.");
        }

        Log::info("Lock acquired for Company ID: {$this->companyId}. Starting depreciation.");

        try {
            Cache::put($this->jobId, ['status' => 'running', 'progress' => 0], now()->addHour());

            // Ambil semua aset yang eligible (Aktif & Belum dijual)
            $assetIds = Asset::withoutGlobalScope(CompanyScope::class)
                ->whereNotIn('status', ['Sold', 'Disposal', 'Onboard'])
                ->where('asset_type', 'FA')
                ->where('start_depre_date', '<=', now())
                ->where('company_id', $this->companyId)
                ->pluck('id');

            $totalOperations = $assetIds->count();

            if ($totalOperations === 0) {
                Cache::put($this->jobId, ['status' => 'completed', 'progress' => 100, 'message' => 'Tidak ada asset yang perlu di depresiasi.'], now()->addHour());
                return;
            }

            $processedCount = 0;
            $types = ['commercial', 'fiscal'];

            foreach ($assetIds as $assetId) {
                try {
                    DB::transaction(function () use ($assetId, $types) {
                        
                        // Lock baris aset agar tidak diedit user saat proses berjalan
                        $asset = Asset::withoutGlobalScope(CompanyScope::class)
                                    ->where('id', $assetId)
                                    ->lockForUpdate()
                                    ->first();

                        if (! $asset) return;

                        foreach ($types as $type) {
                            $usefulLifeCol = $type . '_useful_life_month';
                            $nbvCol = $type . '_nbv';
                            $accumDepreCol = $type . '_accum_depre';

                            // Skip jika umur habis atau nilai buku sudah 0
                            if ($asset->$usefulLifeCol <= 0 || $asset->$nbvCol <= 0) continue;

                            $lastDepreciation = Depreciation::withoutGlobalScope(CompanyScope::class)
                                ->where('asset_id', $asset->id)
                                ->where('type', $type)
                                ->latest('depre_date')
                                ->first();

                            if ($lastDepreciation) {
                                $currentBookValue = $lastDepreciation->book_value;
                                $currentAccumulatedDepre = $lastDepreciation->accumulated_depre;
                                
                                $startDate = Carbon::parse($lastDepreciation->depre_date)->addMonthNoOverflow()->startOfMonth();
                            } else {
                                $currentBookValue = $asset->$nbvCol;
                                $currentAccumulatedDepre = $asset->$accumDepreCol;
                                
                                $startDate = Carbon::parse($asset->start_depre_date)->startOfMonth();
                            }

                            // Tentukan Tanggal Akhir (Bulan Lalu)
                            $cutoff = now();
                            if (! $cutoff->isLastOfMonth()) {
                                $cutoff = $cutoff->subMonth();
                            }
                            $endDate = $cutoff->startOfMonth();

                            // Guard: Jika tanggal mulai melebihi tanggal akhir, skip
                            if ($startDate->gt($endDate)) {
                                continue;
                            }

                            // Hitung Penyusutan Bulanan (Rounding sesuai request)
                            $monthlyDepre = round($asset->acquisition_value / $asset->$usefulLifeCol);
                            
                            $period = CarbonPeriod::create($startDate, '1 month', $endDate);

                            foreach ($period as $date) {
                                if ($currentBookValue <= 0) break;

                                $depreDate = $date->endOfMonth()->toDateString();
                                
                                // Cek Idempotency: Apakah data untuk bulan ini sudah ada?
                                $exists = Depreciation::withoutGlobalScope(CompanyScope::class)
                                    ->where('asset_id', $asset->id)
                                    ->where('type', $type)
                                    ->whereDate('depre_date', $depreDate)
                                    ->exists();

                                if ($exists) {
                                    // Jika sudah ada, update saldo memori kita agar sinkron, lalu skip
                                    $latest = Depreciation::withoutGlobalScope(CompanyScope::class)
                                        ->where('asset_id', $asset->id)
                                        ->where('type', $type)
                                        ->whereDate('depre_date', $depreDate)
                                        ->first();

                                    if ($latest) {
                                        $currentAccumulatedDepre = $latest->accumulated_depre;
                                        $currentBookValue = $latest->book_value;
                                    }
                                    continue; 
                                }

                                // Hitung Nilai Baru
                                $finalDepreciationAmount = $monthlyDepre;
                                // Cegah nilai buku negatif
                                if (($currentBookValue - $monthlyDepre) <= 0) {
                                    $finalDepreciationAmount = $currentBookValue;
                                }

                                $currentBookValue -= $finalDepreciationAmount;
                                $currentAccumulatedDepre += $finalDepreciationAmount;

                                // Simpan Record
                                try {
                                    Depreciation::create([
                                        'asset_id' => $asset->id,
                                        'type' => $type,
                                        'depre_date' => $date->endOfMonth(),
                                        'monthly_depre' => $finalDepreciationAmount,
                                        'accumulated_depre' => $currentAccumulatedDepre,
                                        'book_value' => $currentBookValue,
                                        'company_id' => $asset->company_id,
                                    ]);
                                } catch (QueryException $qe) {
                                    // Tangkap error duplikat (Race Condition)
                                    Log::warning("Duplicate detected for asset {$asset->id}. Skipping.");
                                    continue;
                                }
                            }

                            $asset->update([
                                $accumDepreCol => $currentAccumulatedDepre,
                                $nbvCol        => $currentBookValue,
                            ]);
                        }
                    }, 5); // Retry transaksi 5x

                } catch (Throwable $e) {
                    Log::error("Error processing asset {$assetId}: " . $e->getMessage());
                }

                $processedCount++;
                if ($processedCount % 10 === 0 || $processedCount === $totalOperations) {
                    Cache::put($this->jobId, ['status' => 'running', 'progress' => round(($processedCount / $totalOperations) * 100, 2)], now()->addHour());
                }
            }

            Cache::put($this->jobId, ['status' => 'completed', 'progress' => 100, 'message' => 'Semua asset berhasil di depresiasi.'], now()->addHour());

            $assetsCacheKey = "assets_not_depreciated_{$this->companyId}_{$lmYear}-{$lmMonth}";
            
            $freshAssets = Asset::query()
                ->where('asset_type', 'FA')
                ->whereNotIn('assets.status', ['Sold', 'Onboard', 'Disposal'])
                ->where('commercial_nbv', '>', 0)
                ->where('start_depre_date', '<=', $lastMonth->endOfMonth())
                ->where('company_id', $this->companyId)
                ->whereDoesntHave('depreciations', function ($query) use ($lmYear, $lmMonth, $lastMonth) {
                    $query->where('type', 'commercial')
                        ->whereYear('depre_date', $lmYear)
                        ->whereMonth('depre_date', $lmMonth)
                        ->whereDay('depre_date', $lastMonth->endOfMonth()->day);
                })
                ->get();

            Cache::put($assetsCacheKey, $freshAssets, now()->addHour());
            
            // Bersihkan cache lainnya
            $listKey = "depreciation_data_keys_{$this->companyId}";
            $keys = Cache::get($listKey, []);
            if (!empty($keys)) {
                foreach ($keys as $k) {
                    Cache::forget($k);
                }
                Cache::forget($listKey);
            }

        } catch (Throwable $e) {
            Log::error("Bulk Depreciation Failed: " . $e->getMessage());
            Cache::put($this->jobId, ['status' => 'failed', 'error' => $e->getMessage()], now()->addHour());
            throw $e;
        } finally {
            // RELEASE LOCK DENGAN AMAN
            try {
                if ($this->assetsNotDepLock) {
                    $this->assetsNotDepLock->release();
                }
            } catch (Throwable $e) {
                Log::warning("Failed to release assets-not-depreciated lock: " . $e->getMessage());
            }

            try {
                if ($this->mainLock) {
                    $this->mainLock->release();
                }
            } catch (Throwable $e) {
                Log::warning("Failed to release mainLock: " . $e->getMessage());
            }

            Log::info("Lock(s) released for Company ID: {$this->companyId}.");
        }
    }

    public function failed(Throwable $exception)
    {
        Log::error("RunBulkDepreciation job failed for company {$this->companyId}: " . $exception->getMessage());
        Cache::put($this->jobId, ['status' => 'failed', 'error' => $exception->getMessage()], now()->addHour());
    }
}