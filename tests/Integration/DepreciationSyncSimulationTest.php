<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Depreciation;
use App\Jobs\ProcessAssetDepreciation;
use App\Scopes\CompanyScope;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Simulasi Pengujian Depresiasi SYNCHRONOUS untuk 1000 Aset.
 *
 * Test ini menggantikan mekanisme async (Bus::batch + Queue) dengan
 * memanggil ProcessAssetDepreciation::handle() secara langsung dan
 * berurutan (synchronous), mirip dengan perilaku SYNC queue driver.
 *
 * Tujuan:
 *  - Membuktikan kebenaran kalkulasi pada skala 1000 aset.
 *  - Mengukur waktu eksekusi total proses sync.
 *  - Menguji idempotency saat 1000 aset diproses dua kali.
 *  - Menguji toleransi partial failure pada skala besar.
 *  - Menguji distribusi data (NBV, accumulated) setelah proses selesai.
 */
class DepreciationSyncSimulationTest extends TestCase
{
    /** @var AssetName */
    private AssetName $assetName;

    /** @var Location */
    private Location $location;

    /** @var Department */
    private Department $department;

    /** Jumlah aset dalam simulasi ini */
    private const ASSET_COUNT = 1000;

    /** Ukuran chunk untuk ProcessAssetDepreciation (sama dengan implementasi async) */
    private const CHUNK_SIZE = 50;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();

        $this->assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $this->location   = Location::factory()->create(['company_id' => $this->company->id]);
        $this->department = Department::factory()->create(['company_id' => $this->company->id]);
    }

    // ================================================================
    // HELPER METHODS
    // ================================================================

    /**
     * Buat satu aset dengan nilai default yang dapat di-override.
     */
    private function createAsset(array $overrides = []): Asset
    {
        return Asset::factory()->create(array_merge([
            'company_id'                   => $this->company->id,
            'asset_name_id'                => $this->assetName->id,
            'location_id'                  => $this->location->id,
            'department_id'                => $this->department->id,
            'status'                       => 'Active',
            'asset_type'                   => 'FA',
            'acquisition_value'            => 12_000_000,
            'current_cost'                 => 12_000_000,
            'commercial_useful_life_month' => 12,
            'fiscal_useful_life_month'     => 12,
            'commercial_nbv'               => 12_000_000,
            'fiscal_nbv'                   => 12_000_000,
            'commercial_accum_depre'       => 0,
            'fiscal_accum_depre'           => 0,
            'start_depre_date'             => Carbon::now()->subMonths(3)->startOfMonth(),
        ], $overrides));
    }

    /**
     * Buat banyak aset sekaligus menggunakan factory bulk insert.
     * Mengembalikan collection ID yang berhasil dibuat.
     *
     * @return \Illuminate\Support\Collection
     */
    private function createBulkAssets(int $count, array $overrides = []): \Illuminate\Support\Collection
    {
        $defaults = [
            'company_id'                   => $this->company->id,
            'asset_name_id'                => $this->assetName->id,
            'location_id'                  => $this->location->id,
            'department_id'                => $this->department->id,
            'status'                       => 'Active',
            'asset_type'                   => 'FA',
            'acquisition_value'            => 12_000_000,
            'current_cost'                 => 12_000_000,
            'commercial_useful_life_month' => 12,
            'fiscal_useful_life_month'     => 12,
            'commercial_nbv'               => 12_000_000,
            'fiscal_nbv'                   => 12_000_000,
            'commercial_accum_depre'       => 0,
            'fiscal_accum_depre'           => 0,
            'start_depre_date'             => Carbon::now()->subMonths(3)->startOfMonth(),
        ];

        return Asset::factory()
            ->count($count)
            ->create(array_merge($defaults, $overrides))
            ->pluck('id');
    }

    /**
     * Jalankan seluruh proses depresiasi secara SYNCHRONOUS (tanpa antrian).
     * Memecah asset IDs menjadi chunk, lalu memanggil ProcessAssetDepreciation::handle()
     * satu per satu secara berurutan — meniru perilaku SYNC queue driver.
     *
     * @param  \Illuminate\Support\Collection|array  $assetIds
     * @param  string|null                           $jobStatusId
     * @return array ['processed' => int, 'failed' => int, 'duration_ms' => float]
     */
    private function runDepreciationSync($assetIds, ?string $jobStatusId = null): array
    {
        $jobStatusId  = $jobStatusId ?? 'sync-depre-test-' . $this->company->id;
        $assetIds     = collect($assetIds);
        $chunks       = $assetIds->chunk(self::CHUNK_SIZE);
        $totalChunks  = $chunks->count();
        $failedAssets = 0;
        $chunkIndex   = 0;

        $startTime = microtime(true);

        foreach ($chunks as $chunk) {
            $chunkIndex++;
            Log::info("[SyncSim] Processing chunk {$chunkIndex}/{$totalChunks} — " . $chunk->count() . ' assets.');

            $job = new ProcessAssetDepreciation(
                $this->company->id,
                $chunk->toArray(),
                $jobStatusId
            );

            try {
                $job->handle();
            } catch (\Throwable $e) {
                // Chunk-level error (seharusnya tidak terjadi karena job punya try-catch per aset)
                Log::error("[SyncSim] Chunk {$chunkIndex} failed: " . $e->getMessage());
                $failedAssets += $chunk->count();
            }
        }

        $durationMs = round((microtime(true) - $startTime) * 1000, 2);

        Log::info("[SyncSim] Done. Duration: {$durationMs}ms. Chunks: {$totalChunks}. Failed assets: {$failedAssets}.");

        return [
            'processed'   => $assetIds->count() - $failedAssets,
            'failed'      => $failedAssets,
            'chunks'      => $totalChunks,
            'duration_ms' => $durationMs,
        ];
    }

    // ================================================================
    // TEST 1: SIMULASI UTAMA — 1000 ASET DIPROSES SECARA SYNC
    // ================================================================

    /**
     * Simulasi utama: 1000 aset diproses secara sync.
     *
     * Memverifikasi:
     *  1. Semua 1000 aset mendapatkan record depresiasi commercial.
     *  2. Semua 1000 aset mendapatkan record depresiasi fiscal.
     *  3. Tidak ada duplikasi record.
     *  4. NBV asset setelah proses lebih kecil dari acquisition_value.
     *
     * @group simulation
     * @group slow
     */
    public function test_sync_depreciation_processes_1000_assets_correctly(): void
    {
        $assetIds = $this->createBulkAssets(self::ASSET_COUNT, [
            'start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth(),
        ]);

        $this->assertCount(self::ASSET_COUNT, $assetIds, 'Gagal membuat 1000 aset uji.');

        $result = $this->runDepreciationSync($assetIds);

        // ── Durasi —— hanya informatif (tidak ada assertion waktu agar tidak flaky)
        $this->addToAssertionCount(0); // noop
        Log::info('[SyncSim] Test_1000: duration=' . $result['duration_ms'] . 'ms');

        // ── Semua aset harus punya minimal 1 record commercial ────────────────
        $depreciatedCommercial = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->where('type', 'commercial')
            ->distinct('asset_id')
            ->count('asset_id');

        $this->assertEquals(
            self::ASSET_COUNT,
            $depreciatedCommercial,
            "Hanya {$depreciatedCommercial} dari " . self::ASSET_COUNT . " aset yang mendapat record commercial."
        );

        // ── Semua aset harus punya minimal 1 record fiscal ───────────────────
        $depreciatedFiscal = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->where('type', 'fiscal')
            ->distinct('asset_id')
            ->count('asset_id');

        $this->assertEquals(
            self::ASSET_COUNT,
            $depreciatedFiscal,
            "Hanya {$depreciatedFiscal} dari " . self::ASSET_COUNT . " aset yang mendapat record fiscal."
        );

        // ── Total records = 1000 aset × 1 bulan × 2 tipe = 2000 ──────────────
        $totalRecords = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->count();

        $this->assertEquals(
            self::ASSET_COUNT * 2, // commercial + fiscal per aset
            $totalRecords,
            "Total record seharusnya " . (self::ASSET_COUNT * 2) . ", ditemukan {$totalRecords}."
        );

        // ── NBV asset harus berkurang setelah proses ──────────────────────────
        $assetsWithReducedNbv = Asset::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $assetIds->toArray())
            ->where('commercial_nbv', '<', 12_000_000)
            ->count();

        $this->assertEquals(
            self::ASSET_COUNT,
            $assetsWithReducedNbv,
            "Tidak semua asset memiliki NBV yang berkurang setelah proses depresiasi."
        );
    }

    // ================================================================
    // TEST 2: IDEMPOTENCY — JALANKAN 2X, TIDAK BOLEH ADA DUPLIKASI
    // ================================================================

    /**
     * Idempotency test: 1000 aset diproses 2x, jumlah record harus tetap sama.
     *
     * Ini menguji bahwa depreByDate index (unique constraint) bekerja dengan benar
     * saat operasi diulang pada bulan yang sudah ada record-nya.
     *
     * @group simulation
     * @group slow
     */
    public function test_sync_depreciation_is_idempotent_on_1000_assets(): void
    {
        $assetIds = $this->createBulkAssets(self::ASSET_COUNT, [
            'start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth(),
        ]);

        // Run pertama
        $this->runDepreciationSync($assetIds, 'idempotent-run-1');

        $countAfterFirstRun = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->count();

        $this->assertGreaterThan(0, $countAfterFirstRun, 'Run pertama harus menghasilkan records.');

        // Run kedua — harus menghasilkan count yang sama persis
        $this->runDepreciationSync($assetIds, 'idempotent-run-2');

        $countAfterSecondRun = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->count();

        $this->assertEquals(
            $countAfterFirstRun,
            $countAfterSecondRun,
            "Duplikasi terdeteksi! Run-1: {$countAfterFirstRun}, Run-2: {$countAfterSecondRun}."
        );
    }

    // ================================================================
    // TEST 3: PARTIAL FAILURE — SEBAGIAN ASET RUSAK, SISANYA TETAP JALAN
    // ================================================================

    /**
     * Partial failure tolerance: dari 1000 aset, sebagian start_depre_date = NULL (rusak).
     * Aset yang valid harus tetap diproses, aset rusak tidak boleh menghasilkan record.
     *
     * @group simulation
     * @group slow
     */
    public function test_sync_depreciation_survives_partial_failure_at_1000_scale(): void
    {
        $validCount   = self::ASSET_COUNT - 50; // 950 aset valid
        $defectCount  = 50;                     // 50 aset sengaja dirusak

        $validAssetIds   = $this->createBulkAssets($validCount, [
            'start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth(),
        ]);

        $defectAssetIds  = $this->createBulkAssets($defectCount, [
            'start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth(),
        ]);

        // Rusak start_depre_date menjadi NULL untuk aset defective
        DB::table('assets')
            ->whereIn('id', $defectAssetIds->toArray())
            ->update(['start_depre_date' => null]);

        $allAssetIds = $validAssetIds->merge($defectAssetIds)->shuffle();

        $this->runDepreciationSync($allAssetIds);

        // Aset valid harus punya record commercial
        $validDepreciated = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->whereIn('asset_id', $validAssetIds->toArray())
            ->where('type', 'commercial')
            ->distinct('asset_id')
            ->count('asset_id');

        $this->assertEquals(
            $validCount,
            $validDepreciated,
            "Seharusnya {$validCount} aset valid terdepresiasi, hanya {$validDepreciated} yang berhasil."
        );

        // Aset rusak TIDAK boleh punya record sama sekali
        $defectDepreciated = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->whereIn('asset_id', $defectAssetIds->toArray())
            ->count();

        $this->assertEquals(
            0,
            $defectDepreciated,
            "{$defectDepreciated} aset defective seharusnya tidak punya record depresiasi."
        );
    }

    // ================================================================
    // TEST 4: MULTI-BULAN — 1000 ASET DENGAN RIWAYAT 6 BULAN
    // ================================================================

    /**
     * Multi-month backfill: 1000 aset mulai depresiasi 6 bulan lalu.
     * Setiap aset harus mendapat 6 record commercial + 6 fiscal = 12 record per aset.
     *
     * @group simulation
     * @group slow
     */
    public function test_sync_depreciation_backfills_6_months_for_1000_assets(): void
    {
        $months = 6;

        $assetIds = $this->createBulkAssets(self::ASSET_COUNT, [
            'start_depre_date'             => Carbon::now()->subMonths($months)->startOfMonth(),
            'commercial_useful_life_month' => 60,
            'fiscal_useful_life_month'     => 60,
            'acquisition_value'            => 60_000_000,
            'commercial_nbv'               => 60_000_000,
            'fiscal_nbv'                   => 60_000_000,
        ]);

        $result = $this->runDepreciationSync($assetIds);

        Log::info('[SyncSim] Test_6Month: duration=' . $result['duration_ms'] . 'ms');

        // Total records: 1000 aset × 6 bulan × 2 tipe = 12.000 records
        $totalRecords = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->count();

        $expectedTotal = self::ASSET_COUNT * $months * 2;

        $this->assertEquals(
            $expectedTotal,
            $totalRecords,
            "Expected {$expectedTotal} records, got {$totalRecords}."
        );

        // Verifikasi NBV akhir per aset: 60jt - (6 × 1jt) = 54jt
        $expectedNbv = 60_000_000 - ($months * 1_000_000);

        $assetsWithCorrectNbv = Asset::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $assetIds->toArray())
            ->where('commercial_nbv', $expectedNbv)
            ->count();

        $this->assertEquals(
            self::ASSET_COUNT,
            $assetsWithCorrectNbv,
            "Tidak semua aset memiliki NBV akhir yang benar ({$expectedNbv}). Hanya {$assetsWithCorrectNbv} yang benar."
        );

        // Verifikasi accumulated_depre akhir: 6 × 1jt = 6jt
        $expectedAccum = $months * 1_000_000;

        $assetsWithCorrectAccum = Asset::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $assetIds->toArray())
            ->where('commercial_accum_depre', $expectedAccum)
            ->count();

        $this->assertEquals(
            self::ASSET_COUNT,
            $assetsWithCorrectAccum,
            "Tidak semua aset memiliki accumulated_depre yang benar ({$expectedAccum}). Hanya {$assetsWithCorrectAccum} yang benar."
        );
    }

    // ================================================================
    // TEST 5: ASET BERAGAM — MIX ACQUISITION_VALUE & USEFUL_LIFE
    // ================================================================

    /**
     * Heterogeneous assets: 1000 aset dengan mix acquisition value dan useful life
     * yang berbeda-beda. Memverifikasi tidak ada nilai negatif (NBV tidak pernah < 0)
     * dan setiap aset mendapat setidaknya satu record.
     *
     * @group simulation
     * @group slow
     */
    public function test_sync_depreciation_handles_1000_heterogeneous_assets(): void
    {
        $scenarios = [
            // [acquisition_value, useful_life, start_months_ago]
            [6_000_000,  6,  6],
            [12_000_000, 12, 3],
            [24_000_000, 24, 1],
            [48_000_000, 48, 6],
            [100_000_000, 36, 2],
        ];

        $allAssetIds = collect();
        $perScenario = self::ASSET_COUNT / count($scenarios); // 200 per skenario

        foreach ($scenarios as [$acqVal, $usefulLife, $startMonthsAgo]) {
            $ids = $this->createBulkAssets((int) $perScenario, [
                'acquisition_value'            => $acqVal,
                'current_cost'                 => $acqVal,
                'commercial_useful_life_month' => $usefulLife,
                'fiscal_useful_life_month'     => $usefulLife,
                'commercial_nbv'               => $acqVal,
                'fiscal_nbv'                   => $acqVal,
                'commercial_accum_depre'       => 0,
                'fiscal_accum_depre'           => 0,
                'start_depre_date'             => Carbon::now()->subMonths($startMonthsAgo)->startOfMonth(),
            ]);
            $allAssetIds = $allAssetIds->merge($ids);
        }

        $this->assertCount(self::ASSET_COUNT, $allAssetIds);

        $result = $this->runDepreciationSync($allAssetIds);

        Log::info('[SyncSim] Test_Hetero: duration=' . $result['duration_ms'] . 'ms');

        // Semua 1000 aset harus punya minimal 1 record commercial
        $depreciatedCount = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->where('type', 'commercial')
            ->distinct('asset_id')
            ->count('asset_id');

        $this->assertEquals(
            self::ASSET_COUNT,
            $depreciatedCount,
            "Hanya {$depreciatedCount} aset yang mendapat record commercial dari " . self::ASSET_COUNT . "."
        );

        // Tidak ada NBV yang negatif
        $negativeNbvCount = Asset::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $allAssetIds->toArray())
            ->where('commercial_nbv', '<', 0)
            ->count();

        $this->assertEquals(
            0,
            $negativeNbvCount,
            "{$negativeNbvCount} aset memiliki commercial_nbv negatif — ini bug!"
        );

        // Tidak ada accumulated_depre yang melebihi acquisition_value
        // Fetch assets then filter in PHP untuk menghindari alias tabel yang memicu soft-delete scope
        $overAccumCount = Asset::withoutGlobalScope(CompanyScope::class)
            ->whereIn('id', $allAssetIds->toArray())
            ->get(['id', 'commercial_accum_depre', 'acquisition_value'])
            ->filter(fn($a) => $a->commercial_accum_depre > $a->acquisition_value)
            ->count();

        $this->assertEquals(
            0,
            $overAccumCount,
            "{$overAccumCount} aset memiliki accumulated_depre melebihi acquisition_value."
        );
    }

    // ================================================================
    // TEST 6: PERFORMA — MENGUKUR WAKTU EKSEKUSI SYNC 1000 ASET
    // ================================================================

    /**
     * Performance benchmark: mengukur waktu eksekusi sync depreciation untuk 1000 aset.
     * Tidak ada assertion ketat pada waktu (menghindari flaky test),
     * tetapi mencetak metrik ke log untuk analisis.
     *
     * @group simulation
     * @group benchmark
     * @group slow
     */
    public function test_sync_depreciation_performance_benchmark_1000_assets(): void
    {
        $assetIds = $this->createBulkAssets(self::ASSET_COUNT, [
            'start_depre_date' => Carbon::now()->subMonths(3)->startOfMonth(),
        ]);

        $result = $this->runDepreciationSync($assetIds, 'benchmark-job');

        // Log metrics
        Log::info('[SyncSim][Benchmark] Results', [
            'total_assets'  => self::ASSET_COUNT,
            'chunk_size'    => self::CHUNK_SIZE,
            'total_chunks'  => $result['chunks'],
            'duration_ms'   => $result['duration_ms'],
            'duration_sec'  => round($result['duration_ms'] / 1000, 2),
            'ms_per_asset'  => round($result['duration_ms'] / self::ASSET_COUNT, 2),
            'assets_per_sec' => round(self::ASSET_COUNT / ($result['duration_ms'] / 1000)),
        ]);

        // Verifikasi dasar: proses harus menghasilkan records
        $totalRecords = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->count();

        $this->assertGreaterThan(
            0,
            $totalRecords,
            'Proses benchmark tidak menghasilkan records sama sekali.'
        );

        // Informasi untuk developer (akan muncul di test output jika --verbose)
        $this->addToAssertionCount(1);

        echo sprintf(
            "\n\n[BENCHMARK] 1000 Aset (Sync)\n" .
            "  Waktu total : %.2f detik\n" .
            "  Per aset    : %.2f ms\n" .
            "  Throughput  : %d aset/detik\n" .
            "  Total chunk : %d (@ %d aset/chunk)\n" .
            "  Records DB  : %d\n\n",
            $result['duration_ms'] / 1000,
            $result['duration_ms'] / self::ASSET_COUNT,
            round(self::ASSET_COUNT / ($result['duration_ms'] / 1000)),
            $result['chunks'],
            self::CHUNK_SIZE,
            $totalRecords
        );
    }
}
