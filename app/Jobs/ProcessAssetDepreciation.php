<?php

namespace App\Jobs;

use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Asset;
use App\Models\Depreciation;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessAssetDepreciation implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // 3 percobaan agar bisa recover dari transient error (deadlock, DB timeout).
    // $backoff = 10 detik antar retry agar DB tidak langsung dihantam lagi.
    // Aman diretry karena insert duplikat sudah terproteksi via depreByDate index.
    public $tries = 3;
    public $backoff = 10;
    public $timeout = 600;

    protected $companyId;
    protected $assetIds;
    protected $jobStatusId;

    public function __construct($companyId, $assetIds, $jobStatusId)
    {
        $this->companyId = $companyId;
        $this->assetIds = $assetIds;
        $this->jobStatusId = $jobStatusId;
    }

    public function handle(): void
    {
        // Set config untuk multi-tenancy di background worker
        config(['app.active_company_id' => $this->companyId]);

        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        $types = ['commercial', 'fiscal'];
        $now = now();

        // Tentukan batas akhir periode — selalu awal bulan lalu.
        // Menghindari bug saat job dijalankan tepat di tanggal akhir bulan (isLastOfMonth = true)
        // yang sebelumnya menyebabkan bulan berjalan ikut didepresiasi.
        $endDate = $now->copy()->subMonthNoOverflow()->startOfMonth();

        // --- OPTIMASI: Ambil semua aset dalam 1 query ---
        $assets = Asset::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $this->assetIds)
            ->get()
            ->keyBy('id');

        // --- OPTIMASI: Eager-load semua record depresiasi yang sudah ada ---
        // Dikelompokkan: [asset_id][type][depre_date_Ymd] => record
        $existingDepre = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->whereIn('asset_id', $this->assetIds)
            ->get()
            ->groupBy(fn($d) => $d->asset_id . '_' . $d->type);

        foreach ($this->assetIds as $assetId) {
            try {
                $asset = $assets->get($assetId);
                if (!$asset)
                    continue;

                // Fix #2: Kumpulkan semua perubahan kolom per aset di sini,
                // lalu jalankan 1 UPDATE saja di akhir (setelah loop types).
                $assetColumnsToUpdate = [];

                foreach ($types as $type) {
                    $usefulLifeCol = $type . '_useful_life_month';
                    $nbvCol = $type . '_nbv';
                    $accumDepreCol = $type . '_accum_depre';

                    // Skip jika umur manfaat atau nilai perolehan tidak valid
                    if ($asset->$usefulLifeCol <= 0 || $asset->acquisition_value <= 0) {
                        continue;
                    }

                    // Ambil existing records dari memory (sudah di-load sebelumnya)
                    $groupKey = $assetId . '_' . $type;
                    $assetDepreGroup = $existingDepre->get($groupKey);

                    // Bangun index dalam memory: 'Y-m-d' => record
                    $depreByDate = [];
                    $lastRecord = null;
                    if ($assetDepreGroup) {
                        foreach ($assetDepreGroup->sortBy('depre_date') as $rec) {
                            $depreByDate[$rec->depre_date] = $rec;
                            if (!$lastRecord || $rec->depre_date > $lastRecord->depre_date) {
                                $lastRecord = $rec;
                            }
                        }
                    }

                    // Tentukan starting point
                    if ($lastRecord) {
                        $currentBookValue = $lastRecord->book_value;
                        $currentAccumulatedDepre = $lastRecord->accumulated_depre;
                        $startDate = Carbon::parse($lastRecord->depre_date)->addMonthNoOverflow()->startOfMonth();
                    } else {
                        // Tidak ada riwayat — mulai dari acquisition_value penuh
                        $currentBookValue = $asset->acquisition_value;
                        $currentAccumulatedDepre = 0;
                        $startDate = Carbon::parse($asset->start_depre_date)->startOfMonth();
                    }

                    // Jika sudah lunas (book value = 0), skip
                    if ($currentBookValue <= 0) {
                        continue;
                    }

                    if ($startDate->gt($endDate)) {
                        continue;
                    }

                    $period = CarbonPeriod::create($startDate, '1 month', $endDate);

                    // Fix #1: Hitung origin untuk elapsed months menggunakan integer arithmetic
                    // agar akurat 100% tanpa tergantung pada diffInMonths() yang bisa meleset
                    // saat cross month-end (misal tanggal 31 ke bulan 30-hari).
                    $assetStartYear = (int) Carbon::parse($asset->start_depre_date)->format('Y');
                    $assetStartMonth = (int) Carbon::parse($asset->start_depre_date)->format('m');

                    // Kumpulkan rows baru untuk bulk insert
                    $toInsert = [];
                    $finalBookValue = $currentBookValue;
                    $finalAccumulatedDep = $currentAccumulatedDepre;

                    foreach ($period as $date) {
                        if ($currentBookValue <= 0)
                            break;

                        $depreDateStr = $date->copy()->endOfMonth()->toDateString();

                        // Cek dari memory (sudah load semua)
                        if (isset($depreByDate[$depreDateStr])) {
                            $existing = $depreByDate[$depreDateStr];
                            $currentAccumulatedDepre = $existing->accumulated_depre;
                            $currentBookValue = $existing->book_value;
                            continue;
                        }

                        // Fix #1: Elapsed months via integer arithmetic (year × 12 + month),
                        // menggantikan diffInMonths() yang tidak akurat di cross-month boundaries.
                        $curYear = (int) $date->format('Y');
                        $curMonth = (int) $date->format('m');
                        $monthsPassed = (($curYear - $assetStartYear) * 12) + ($curMonth - $assetStartMonth) + 1;

                        if ($monthsPassed >= $asset->$usefulLifeCol) {
                            $targetAccumulatedDepre = $asset->acquisition_value;
                        } else {
                            $targetAccumulatedDepre = round(($asset->acquisition_value / $asset->$usefulLifeCol) * $monthsPassed);
                        }

                        // Guard-rail: tidak boleh melebihi acquisition_value atau turun dari current
                        if ($targetAccumulatedDepre > $asset->acquisition_value) {
                            $targetAccumulatedDepre = $asset->acquisition_value;
                        }
                        if ($targetAccumulatedDepre < $currentAccumulatedDepre) {
                            $targetAccumulatedDepre = $currentAccumulatedDepre;
                        }

                        $finalDepreciationAmount = $targetAccumulatedDepre - $currentAccumulatedDepre;

                        if ($finalDepreciationAmount > $currentBookValue) {
                            $finalDepreciationAmount = $currentBookValue;
                        }

                        $currentBookValue -= $finalDepreciationAmount;
                        $currentAccumulatedDepre += $finalDepreciationAmount;

                        $toInsert[] = [
                            'asset_id' => $assetId,
                            'type' => $type,
                            'depre_date' => $depreDateStr,
                            'monthly_depre' => $finalDepreciationAmount,
                            'accumulated_depre' => $currentAccumulatedDepre,
                            'book_value' => $currentBookValue,
                            'company_id' => $asset->company_id,
                        ];

                        $finalBookValue = $currentBookValue;
                        $finalAccumulatedDep = $currentAccumulatedDepre;
                    }

                    // Bulk insert depreciations (tanpa update asset — dikumpulkan dulu)
                    if (!empty($toInsert)) {
                        DB::table('depreciations')->insert($toInsert);

                        // Fix #2: Kumpulkan kolom yang perlu diupdate, eksekusi nanti 1x
                        $assetColumnsToUpdate[$accumDepreCol] = $finalAccumulatedDep;
                        $assetColumnsToUpdate[$nbvCol] = $finalBookValue;
                    }
                }

                // Fix #2: Update asset 1x saja di akhir, setelah commercial + fiscal selesai.
                // Sebelumnya: 2 UPDATE queries (1 per type). Sekarang: 1 UPDATE saja per aset.
                if (!empty($assetColumnsToUpdate)) {
                    DB::transaction(function () use ($assetId, $assetColumnsToUpdate) {
                        Asset::withoutGlobalScope(CompanyScope::class)
                            ->where('id', $assetId)
                            ->update($assetColumnsToUpdate);
                    });
                }

            } catch (Throwable $e) {
                Log::error("Error processing asset {$assetId}: " . $e->getMessage(), [
                    'trace' => $e->getTraceAsString(),
                ]);

                // Opsi A: Catat asset yang gagal ke cache agar bisa dilaporkan ke user.
                // Key ini dibaca oleh RunBulkDepreciation batch then() untuk menentukan
                // apakah ada partial failure yang perlu ditampilkan ke user.
                $failedKey = "depreciation_failed_assets_{$this->companyId}";
                $failedList = Cache::get($failedKey, []);
                if (!in_array($assetId, $failedList)) {
                    $failedList[] = $assetId;
                    Cache::put($failedKey, $failedList, now()->addDay());
                }
            }
        }

        // Update progress di cache agar progress bar bisa berjalan
        if ($this->batch()) {
            $batch = $this->batch();
            $progress = $batch->progress();
            $statusData = \Illuminate\Support\Facades\Cache::get($this->jobStatusId, []);
            if (isset($statusData['status']) && $statusData['status'] !== 'completed') {
                $statusData['progress'] = $progress;
                $statusData['message'] = "Memproses... ({$progress}%)";
                \Illuminate\Support\Facades\Cache::put($this->jobStatusId, $statusData, now()->addHour());
            }
        }
    }
}
