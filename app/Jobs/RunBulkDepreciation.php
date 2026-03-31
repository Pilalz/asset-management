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
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;
use Throwable;

class RunBulkDepreciation implements ShouldQueue
{
    use Queueable, Dispatchable, InteractsWithQueue, SerializesModels;

    // $tries = 1: Orchestrator TIDAK boleh diretry otomatis.
    // Jika retry saat batch sudah terdispatch sebagian, bisa terjadi duplikasi chunk.
    // Error handling dilakukan via cache status 'failed' + method failed() di bawah.
    public $tries = 1;
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
        config(['app.active_company_id' => $this->companyId]);

        $mainLockKey = 'running-depreciation-process:' . $this->companyId;
        $mainLock = Cache::lock($mainLockKey, 3600);

        try {
            $mainLock->block(2);
        } catch (LockTimeoutException $e) {
            Log::warning("LockTimeout: Proses lain sedang berjalan untuk Company ID: {$this->companyId}. Job akan dicoba lagi nanti.");
            $this->release(30);
            return;
        }

        $lastMonth = Carbon::now()->subMonthNoOverflow();
        $lmYear = $lastMonth->year;
        $lmMonth = $lastMonth->month;
        $assetsLockKey = "lock_assets_not_depreciated_{$this->companyId}_{$lmYear}-{$lmMonth}";

        $assetsNotDepLock = Cache::lock($assetsLockKey, 3600);
        try {
            $assetsNotDepLock->block(2);
        } catch (LockTimeoutException $e) {
            // Fix #1: Release mainLock sebelum return agar tidak terjadi deadlock 3600 detik
            Log::warning("LockTimeout: could not acquire assets-not-depreciated lock for Company ID: {$this->companyId}. Retrying.");
            $mainLock->forceRelease();
            $this->release(30);
            return;
        }

        Log::info("Lock acquired for Company ID: {$this->companyId}. Starting depreciation batching.");

        $cachedStatus = Cache::get($this->jobId);
        if ($cachedStatus && isset($cachedStatus['status']) && $cachedStatus['status'] === 'completed') {
            Log::info("Job for Company {$this->companyId} already completed by another worker. Exiting.");
            $mainLock->forceRelease();
            $assetsNotDepLock->forceRelease();
            return;
        }

        try {
            Cache::put($this->jobId, ['status' => 'running', 'progress' => 0], now()->addHour());

            $assetIds = Asset::withoutGlobalScope(CompanyScope::class)
                ->whereNotIn('status', ['Sold', 'Disposal', 'Onboard'])
                ->where('asset_type', 'FA')
                ->where('start_depre_date', '<=', now())
                ->where('company_id', $this->companyId)
                ->pluck('id');

            $totalOperations = $assetIds->count();

            if ($totalOperations === 0) {
                Cache::put($this->jobId, ['status' => 'completed', 'progress' => 100, 'message' => 'Tidak ada asset yang perlu di depresiasi.'], now()->addHour());
                $mainLock->forceRelease();
                $assetsNotDepLock->forceRelease();
                return;
            }

            // Memecah menjadi potongan kecil agar dieksekusi oleh ProcessAssetDepreciation
            $chunks = $assetIds->chunk(500);
            $jobs = [];

            foreach ($chunks as $chunk) {
                $jobs[] = new ProcessAssetDepreciation($this->companyId, $chunk->toArray(), $this->jobId);
            }

            $companyId = $this->companyId;
            $jobId = $this->jobId;

            Bus::batch($jobs)
                ->name('Depreciation Data: ' . $companyId)
                ->then(function (Batch $batch) use ($companyId, $jobId, $lmYear, $lmMonth) {
                    // Cek partial failure: baca daftar aset yang gagal dari cache
                    $failedKey = "depreciation_failed_assets_{$companyId}";
                    $failedList = Cache::get($failedKey, []);

                    if (!empty($failedList)) {
                        // Ada partial failure — selesai tapi tidak 100% sempurna
                        Cache::put($jobId, [
                            'status' => 'completed_with_errors',
                            'progress' => 100,
                            'message' => 'Proses selesai, namun ' . count($failedList) . ' aset gagal diproses. Silakan cek log untuk detail.',
                            'failed_count' => count($failedList),
                            'failed_ids' => $failedList,
                        ], now()->addHour());
                        Log::warning("Depreciation completed with errors for Company {$companyId}. Failed asset IDs: " . implode(', ', $failedList));
                    } else {
                        // Semua aset berhasil
                        Cache::put($jobId, [
                            'status' => 'completed',
                            'progress' => 100,
                            'message' => 'Semua asset berhasil di depresiasi.',
                        ], now()->addHour());
                        Log::info("Depreciation batch completed for Company ID: $companyId");
                    }

                    // Invalidasi cache tombol "RunAllDepre" agar langsung hilang dari UI
                    $pendingCacheKey = "has_assets_pending_depreciation_{$companyId}_{$lmYear}-{$lmMonth}";
                    Cache::forget($pendingCacheKey);
                    Log::info("Cache invalidated: {$pendingCacheKey}");
                })
                ->catch(function (Batch $batch, Throwable $e) use ($jobId) {
                    Cache::put($jobId, ['status' => 'failed', 'error' => $e->getMessage()], now()->addHour());
                })
                ->finally(function (Batch $batch) use ($companyId, $assetsLockKey, $mainLockKey, $lmYear, $lmMonth, $lastMonth) {
                    // Membersihkan Gembok Cache karena semua Job telah selesai
                    Cache::lock($assetsLockKey)->forceRelease();
                    Cache::lock($mainLockKey)->forceRelease();
                    Log::info("Locks released via batch finally for Company ID: $companyId");

                    $listKey = "depreciation_data_keys_{$companyId}";
                    $keys = Cache::get($listKey, []);
                    if (!empty($keys)) {
                        foreach ($keys as $k) {
                            Cache::forget($k);
                        }
                        Cache::forget($listKey);
                    }

                    // Bersihkan cache failed assets agar tidak stale pada run berikutnya
                    Cache::forget("depreciation_failed_assets_{$companyId}");

                    // Revalidate Cache Dashboard Not-Depreciated
                    $assetsCacheKey = "assets_not_depreciated_{$companyId}_{$lmYear}-{$lmMonth}";
                    $freshAssets = Asset::query()
                        ->withoutGlobalScope(CompanyScope::class)
                        ->where('asset_type', 'FA')
                        ->whereNotIn('assets.status', ['Sold', 'Onboard', 'Disposal'])
                        ->where('commercial_nbv', '>', 0)
                        ->where('start_depre_date', '<=', $lastMonth->endOfMonth())
                        ->where('company_id', $companyId)
                        ->whereDoesntHave('depreciations', function ($query) use ($lmYear, $lmMonth, $lastMonth) {
                            $query->withoutGlobalScope(CompanyScope::class)
                                ->where('type', 'commercial')
                                ->whereYear('depre_date', $lmYear)
                                ->whereMonth('depre_date', $lmMonth)
                                ->whereDay('depre_date', $lastMonth->endOfMonth()->day);
                        })
                        ->get();

                    Cache::put($assetsCacheKey, $freshAssets, now()->addHour());
                })
                ->dispatch();

        } catch (Throwable $e) {
            Log::error("Bulk Depreciation Failed: " . $e->getMessage());
            Cache::put($this->jobId, ['status' => 'failed', 'error' => $e->getMessage()], now()->addHour());

            $mainLock->forceRelease();
            $assetsNotDepLock->forceRelease();

            throw $e;
        }
    }

    public function failed(Throwable $exception)
    {
        Log::error("RunBulkDepreciation job failed for company {$this->companyId}: " . $exception->getMessage());
        Cache::put($this->jobId, ['status' => 'failed', 'error' => $exception->getMessage()], now()->addHour());
    }
}