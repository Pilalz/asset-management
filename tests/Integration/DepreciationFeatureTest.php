<?php

namespace Tests\Integration;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Depreciation;
use App\Jobs\RunBulkDepreciation;
use App\Jobs\ProcessAssetDepreciation;
use App\Scopes\CompanyScope;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Gabungan skenario Integration Testing untuk fitur Depresiasi.
 * Termasuk Job ProcessAssetDepreciation dan RunBulkDepreciation.
 */
class DepreciationFeatureTest extends TestCase
{
    /** @var AssetName */
    private AssetName $assetName;

    /** @var Location */
    private Location $location;

    /** @var Department */
    private Department $department;

    /**
     * Setup environment sebelum setiap pengujian dijalankan.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();

        $this->assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $this->location = Location::factory()->create(['company_id' => $this->company->id]);
        $this->department = Department::factory()->create(['company_id' => $this->company->id]);
    }

    /**
     * Helper untuk membuat data aset uji coba dengan nilai default.
     */
    private function createAsset(array $overrides = []): Asset
    {
        return Asset::factory()->create(array_merge([
            'asset_number' => 'AST-INT-' . rand(100, 999),
            'company_id' => $this->company->id,
            'asset_name_id' => $this->assetName->id,
            'location_id' => $this->location->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
            'asset_type' => 'FA',
            'acquisition_value' => 12_000_000,
            'current_cost' => 12_000_000,
            'commercial_useful_life_month' => 12,
            'fiscal_useful_life_month' => 12,
            'commercial_nbv' => 12_000_000,
            'fiscal_nbv' => 12_000_000,
            'commercial_accum_depre' => 0,
            'fiscal_accum_depre' => 0,
            'start_depre_date' => Carbon::now()->subMonths(3)->startOfMonth(),
        ], $overrides));
    }

    /**
     * Helper untuk langsung memicu background job per aset tanpa antrian.
     */
    private function dispatchSingleAssetJob(Asset $asset): void
    {
        $job = new ProcessAssetDepreciation(
            $this->company->id,
            [$asset->id],
            'test-job-status-' . $asset->id
        );
        $job->handle();
    }

    // ================================================================
    // PART 1: CORE JOB CALCULATION (RunBulkDepreciation)
    // ================================================================

    /**
     * Menguji Job menghitung penyusutan komersial dengan tepat dan memperbarui data master aset.
     */
    public function test_bulk_job_calculates_commercial_depreciation_correctly(): void
    {
        $asset = $this->createAsset(['start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth()]);
        Cache::forget('running-depreciation-process:' . $this->company->id);

        $job = new RunBulkDepreciation($this->company->id);
        $job->handle();

        $count = Depreciation::where('asset_id', $asset->id)->where('type', 'commercial')->count();
        $this->assertGreaterThanOrEqual(1, $count);

        $firstDepre = Depreciation::where('asset_id', $asset->id)
            ->where('type', 'commercial')
            ->orderBy('depre_date', 'asc')
            ->first();

        // 12jt / 12 = 1jt
        $this->assertEquals(1000000, $firstDepre->monthly_depre);

        $asset->refresh();
        $this->assertLessThan(12000000, $asset->commercial_nbv);
        $this->assertEquals($asset->acquisition_value - ($count * 1000000), $asset->commercial_nbv);
    }

    /**
     * Menguji Job menghitung penyusutan fiskal dengan tepat dan memperbarui data fiskal aset.
     */
    public function test_bulk_job_calculates_fiscal_depreciation_correctly(): void
    {
        $asset = $this->createAsset(['start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth()]);
        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        $count = Depreciation::where('asset_id', $asset->id)->where('type', 'fiscal')->count();
        $this->assertGreaterThanOrEqual(1, $count);

        $firstDepre = Depreciation::where('asset_id', $asset->id)->where('type', 'fiscal')->first();
        $this->assertEquals(1000000, $firstDepre->monthly_depre);
    }

    // ================================================================
    // PART 2: IDEMPOTENCY & SKIP LOGIC (ProcessAssetDepreciation)
    // ================================================================

    /**
     * Menguji bahwa jika job dijalankan 2x berturut-turut pada bulan yang sama, tidak akan dibuat double record (Idempotency).
     */
    public function test_running_job_twice_does_not_duplicate_depreciation_records(): void
    {
        $asset = $this->createAsset();

        // Run pertama
        $this->dispatchSingleAssetJob($asset);
        $countAfterFirstRun = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('asset_id', $asset->id)
            ->count();
        $this->assertGreaterThan(0, $countAfterFirstRun);

        // Run kedua
        $this->dispatchSingleAssetJob($asset);
        $countAfterSecondRun = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('asset_id', $asset->id)
            ->count();

        $this->assertEquals($countAfterFirstRun, $countAfterSecondRun, 'Data duplikat terdeteksi pada run kedua.');
    }

    /**
     * Menguji bahwa job aman dilalui jika bulan tersebut sudah ada rekam jejak depresiasinya (Book value lama terjaga).
     */
    public function test_job_preserves_existing_depreciation_values_on_re_run(): void
    {
        $asset = $this->createAsset(['start_depre_date' => Carbon::now()->subMonths(2)->startOfMonth()]);
        $this->dispatchSingleAssetJob($asset);

        $firstRecord = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('asset_id', $asset->id)->where('type', 'commercial')->first();
        $originalBookValue = $firstRecord->book_value;

        // Run ulang
        $this->dispatchSingleAssetJob($asset);

        $reloadedRecord = Depreciation::withoutGlobalScope(CompanyScope::class)->find($firstRecord->id);
        $this->assertEquals($originalBookValue, $reloadedRecord->book_value);
    }

    /**
     * Menguji bahwa aset yang telah lunas (Book Value = 0) akan dilewati oleh sistem komputasi (Skip).
     */
    public function test_fully_depreciated_asset_is_skipped(): void
    {
        $asset = $this->createAsset([
            'commercial_useful_life_month' => 6,
            'commercial_nbv' => 0,
            'commercial_accum_depre' => 12_000_000,
            'start_depre_date' => Carbon::now()->subMonths(7)->startOfMonth(),
        ]);

        // Berikan riwayat record dummy sehingga ProcessAssetDepreciation melihat buku terakhir adalah lunas
        Depreciation::withoutGlobalScope(CompanyScope::class)->create([
            'asset_id' => $asset->id,
            'type' => 'commercial',
            'depre_date' => Carbon::now()->subMonths(2)->endOfMonth()->toDateString(),
            'monthly_depre' => 2000000,
            'accumulated_depre' => 12_000_000,
            'book_value' => 0,
            'company_id' => $this->company->id,
        ]);

        $countBefore = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('asset_id', $asset->id)->where('type', 'commercial')->count();

        $this->dispatchSingleAssetJob($asset);

        $countAfter = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('asset_id', $asset->id)->where('type', 'commercial')->count();

        $this->assertEquals($countBefore, $countAfter, 'Aset yang lunas tidak boleh mendapat record tambahan di-depresiasi.');
    }

    /**
     * Menguji bahwa aset yang umur manfaatnya (Useful Life) = 0 akan dilewati (Skip).
     */
    public function test_asset_with_zero_useful_life_is_skipped(): void
    {
        $asset = $this->createAsset(['commercial_useful_life_month' => 0, 'fiscal_useful_life_month' => 0]);
        $this->dispatchSingleAssetJob($asset);

        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Menguji bahwa aset yang nilai perolehannya (Acquisition Value) = 0 akan dilewati (Skip).
     */
    public function test_asset_with_zero_acquisition_value_is_skipped(): void
    {
        $asset = $this->createAsset(['acquisition_value' => 0]);
        $this->dispatchSingleAssetJob($asset);

        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }

    // ================================================================
    // PART 3: ACCURACY, ROUNDING & CONCURRENCY
    // ================================================================

    /**
     * Menguji agar hasil depresiasi tidak akan membuat NBV menjadi negatif. (Pemotongan / Capping di bulan terakhir).
     */
    public function test_depreciation_is_capped_at_remaining_nbv(): void
    {
        $asset = $this->createAsset([
            'commercial_nbv' => 500000, // Sisa dikit
            'start_depre_date' => Carbon::now()->subMonths(12)->startOfMonth(), // Aset sudah berumur 12 bulan
        ]);

        // Buat record sebelumnya (2 bulan lalu) agar sistem melanjutkan hitungan untuk bulan lalu
        Depreciation::withoutGlobalScope(CompanyScope::class)->create([
            'asset_id' => $asset->id,
            'type' => 'commercial',
            'depre_date' => Carbon::now()->subMonths(2)->endOfMonth()->toDateString(),
            'monthly_depre' => 1000000,
            'accumulated_depre' => $asset->acquisition_value - 500000,
            'book_value' => 500000,
            'company_id' => $this->company->id,
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);
        (new RunBulkDepreciation($this->company->id))->handle();

        $latestDepre = Depreciation::where('asset_id', $asset->id)
            ->where('type', 'commercial')
            ->orderBy('depre_date', 'desc')
            ->first();

        $this->assertEquals(500000, $latestDepre->monthly_depre);
        $this->assertEquals(0, $asset->refresh()->commercial_nbv);
    }

    /**
     * Menguji keakuratan perhitungan bulanan menggunakan straight line dan pembulatan keatas jika ada desimal.
     */
    public function test_monthly_depreciation_formula_accuracy_rounding(): void
    {
        $asset = $this->createAsset([
            'acquisition_value' => 10_000_000,
            'commercial_useful_life_month' => 36, // 10jt / 36 = 277.778
            'fiscal_useful_life_month' => 36,     // Agar record fiscal juga match jika ditarik
            'commercial_nbv' => 10_000_000,
            'start_depre_date' => now()->subMonth()->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);
        (new RunBulkDepreciation($this->company->id))->handle();

        $depre = Depreciation::where('asset_id', $asset->id)->where('type', 'commercial')->first();

        $this->assertEquals(277778, (int) $depre->monthly_depre);
    }

    /**
     * Menguji fitur locking (Concurrency Control) agar sistem menolak run bulk jika proses sebelumnya masih berjalan.
     */
    public function test_job_respects_concurrency_lock(): void
    {
        $lockKey = 'running-depreciation-process:' . $this->company->id;
        $lock = Cache::lock($lockKey, 60);
        $lock->get();

        $job = new RunBulkDepreciation($this->company->id);
        $job->handle();

        $this->assertEquals(0, Depreciation::count());
        $lock->release();
    }

    /**
     * Menguji rollback otomatis jika database mengalami kegagalan (Atomicity).
     */
    public function test_depreciation_rollback_on_failure(): void
    {
        $asset = $this->createAsset(['commercial_nbv' => 5_000_000]);
        $originalNbv = $asset->commercial_nbv;

        Cache::forget('running-depreciation-process:' . $this->company->id);

        DB::shouldReceive('transaction')->andThrow(new \Exception('Simulated DB Failure'));

        try {
            (new RunBulkDepreciation($this->company->id))->handle();
        } catch (\Exception $e) {
        }

        $this->assertEquals(0, Depreciation::where('asset_id', $asset->id)->count());
        $this->assertEquals($originalNbv, $asset->refresh()->commercial_nbv);
    }

    /**
     * Menguji perhitungan bulan berjalan untuk aset yang mulai depresiasi di tahun sebelumnya (Lintas Tahun).
     * Contoh: Mulai Desember 2024, dihitung pada Januari 2025.
     */
    public function test_cross_year_depreciation_calculation(): void
    {
        // Simulasi kita sedang berada di bulan Januari 2025
        // Agar bulan berjalan dan bulan lalu terakumulasi, kita buat Job ini berjalan 
        // dengan asumsi endOfMonth adalah Desember, dan endDate di target akhir
        $currentDate = Carbon::create(2025, 1, 31);
        Carbon::setTestNow($currentDate);

        // Aset mulai disusutkan 1 bulan lalu (Desember 2024 -> start_depre_date = 2024-12-01)
        // Karena `endDate` pada ProcessAssetDepreciation adalah bulan lalu dari `now` (yaitu Desember 2024),
        // loop hanya akan memproses bulan Desember 2024 saja (menjadi 1 record).

        $asset = clone $this->createAsset([
            'start_depre_date' => Carbon::create(2024, 12, 1),
            'commercial_useful_life_month' => 48,
            'fiscal_useful_life_month' => 48,
            'acquisition_value' => 48_000_000,
            'commercial_nbv' => 48_000_000,
            'fiscal_nbv' => 48_000_000,
            'commercial_accum_depre' => 0,
            'fiscal_accum_depre' => 0,
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);

        $job = new RunBulkDepreciation($this->company->id);
        $job->handle();

        // Harus ada 2 record (Desember 2024 dan Januari 2025) untuk tiap tipe
        $commercialRecords = Depreciation::where('asset_id', $asset->id)->where('type', 'commercial')->orderBy('depre_date', 'asc')->get();

        // Record komersial hanya boleh muncul bulan Desember (karena endDate di ProcessAssetJob adalah subMonth)
        $this->assertCount(1, $commercialRecords);

        // Record pertama di tahun 2024
        $this->assertEquals(2024, Carbon::parse($commercialRecords[0]->depre_date)->year);
        $this->assertEquals(12, Carbon::parse($commercialRecords[0]->depre_date)->month);

        // Perhitungan: 48jt / 48 bulan = 1.000.000 per bulan
        $this->assertEquals(1000000, $commercialRecords[0]->monthly_depre);

        // Validasi lintas tahun: Jika kita ubah `Now` perlahan menuju Februari 2025,
        // akan ada hitungan Januari
        Carbon::setTestNow(Carbon::create(2025, 2, 28));
        Cache::forget('running-depreciation-process:' . $this->company->id);
        (new RunBulkDepreciation($this->company->id))->handle();

        $commercialRecords = Depreciation::where('asset_id', $asset->id)->where('type', 'commercial')->orderBy('depre_date', 'asc')->get();

        $this->assertCount(2, $commercialRecords);

        // Record kedua di tahun 2025
        $this->assertEquals(2025, Carbon::parse($commercialRecords[1]->depre_date)->year);
        $this->assertEquals(1, Carbon::parse($commercialRecords[1]->depre_date)->month);

        $this->assertEquals(1000000, $commercialRecords[1]->monthly_depre);

        // Akumulasi penyusutan di record terakhir (Januari) harus 2 juta
        $this->assertEquals(2000000, $commercialRecords[1]->accumulated_depre);

        // NBV di record terakhir harus 46 juta
        $this->assertEquals(46000000, $commercialRecords[1]->book_value);

        Carbon::setTestNow(); // Reset waktu test
    }

    /**
     * Menguji distribusi Batch Chunking untuk menangani beban tinggi.
     * Membuat 150 aset dan memastikan seluruh aset sukses diproses (chunk by 100).
     */
    public function test_bulk_depreciation_handles_150_assets_via_chunking(): void
    {
        // Buat 150 aset tambahan di luar setup awal
        for ($i = 0; $i < 150; $i++) {
            $this->createAsset([
                'asset_number' => 'AST-B-' . uniqid() . '-' . $i,
                'start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth(),
            ]);
        }

        Cache::forget('running-depreciation-process:' . $this->company->id);

        $job = new RunBulkDepreciation($this->company->id);
        $job->handle();

        // 150 aset baru + minimal aset uji lain yang mungkin ada selama testing class-level.
        // Kita cukup verifikasi bahwa total aset yang ada saat ini semuanya terdepresiasi.
        $totalActiveAssets = Asset::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->where('status', 'Active')
            ->count();

        $this->assertGreaterThanOrEqual(150, $totalActiveAssets);

        // Cek bahwa jumlah record depresiasi commercial unik berdasarkan asset_id sesuai total aset
        $depreciatedAssetCount = Depreciation::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $this->company->id)
            ->where('type', 'commercial')
            ->distinct('asset_id')
            ->count('asset_id');

        $this->assertEquals($totalActiveAssets, $depreciatedAssetCount);
    }

    /**
     * Menguji toleransi kesalahan sebagian (Partial Failure Tolerance).
     * Jika memproses ribuan aset menggunakan bulk, apabila 1 aset gagal update karena QueryException,
     * Job master/orkestrator tidak boleh crash dan aset yang valid lainnya harus tetap berhasil diproses.
     */
    public function test_bulk_depreciation_survives_partial_failure_of_single_asset(): void
    {
        // Buat 3 Aset baru
        $assetNormal1 = $this->createAsset(['start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth()]);
        $assetDefective = $this->createAsset(['start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth()]);
        $assetNormal2 = $this->createAsset(['start_depre_date' => Carbon::now()->subMonths(1)->startOfMonth()]);

        // Normal behavior untuk semua aset kecuali assetDefective
        // Untuk assetDefective, kita akan injeksikan error saat menghandle

        Cache::forget('running-depreciation-process:' . $this->company->id);

        // Secara default pada PHPUnit, Exception yang tidak ter-catch akan menggagalkan test.
        // Berdasarkan logic di `ProcessAssetDepreciation.php`, error DB di dalam Loop akan
        // ditangkap via Block `try {} catch (\Throwable $e)` yang mana akan meng-log error 
        // tersebut dan melanjutkan proses aset sisanya dalam iterasi foreach yang sama.

        // Kita cukup membuat satu Asset yang pastinya ter-crash apabila diakses,
        // Alih-alih melanggar database constraint (yang akan mengorbankan Test jika via Eloquent Update), 
        // Kita mock model object Asset sehingga saat `get()` dieksekusi oleh ProcessAssetDepreciation memanggil throw exception.

        // Mari kita inject property getter buatan via event hook untuk `assetDefective`.
        // Jika Job ProcessAssetDepreciation dipanggil, ia akan Load asset via $assets->get($assetId).
        // Kita intercept logic Eloquent secara dinamis.

        // Karena eksekusi insert dilakukan massal dengan array binding per bulan,
        // menggunakan DB::listen seringkali lolos jika format array binding-nya kompleks,
        // atau jika exception terlempar di luar block try-catch yang membungkus foreach.
        // Di ProcessAssetDepreciation, loop `foreach ($this->assetIds as $assetId)` 
        // membungkus logika, sehingga try-catch berada DI DALAM loop.

        // Cara terbaik menginduksi error agar try{}catch(\Throwable) bekerja:
        // Kita timpa runtime method `getAttribute` atau scope Model melalui Mockery overload
        // TIDAK BISA KARENA ALREADY EXISTS ERROR.

        // Sebagai gantinya, manfaatkan celah di ProcessAssetDepreciation Baris 90:
        // `if ($asset->$usefulLifeCol <= 0 || $asset->acquisition_value <= 0) { continue; }`
        // 
        // Jika kita paksa set string pada `acquisition_value` di SQLite,
        // SQLite *akan* merubahnya menjadi integer 0 saat operasi aritmatika, 
        // dan itu hanya men-trigger skip (continue), bukan ERROR yang dikirim ke log.
        // ProcessAssetDepreciation membungkus logic aritmatika secara sangat aman.

        // SOLUSI PALING TEPAT:
        // Supaya *seluruh* pemrosesan aset ini gagal dan TIDAK ADA SATUPUN (baik commercial maupun fiscal) 
        // record yang ter-insert, kita harus memicu Exception SEBELUM `$toInsert` di-exec.
        // Di awal loop ProcessAssetDepreciation ada line ini:
        // `$assetStartYear = (int) Carbon::parse($asset->start_depre_date)->format('Y');`
        // Jika kita ganti `start_depre_date` menjadi null langsung via DB RAW UPDATE, 
        // Eloquent model akan load nullable string dan Carbon::parse(null) akan melemparkan exception.
        // ProcessAssetDepreciation tidak memiliki check `if (!$asset->start_depre_date)`.

        DB::statement('UPDATE assets SET start_depre_date = NULL WHERE id = ?', [$assetDefective->id]);

        Depreciation::truncate();

        // Run
        $job = new RunBulkDepreciation($this->company->id);

        try {
            $job->handle();
        } catch (\Exception $e) {
            // Kita mengharapkan Master Job TIDAK crash karena ProcessAssetDepreciation punya try-catch per aset
            // atau exception ter-isolated per chunk (bergantung arsitektur Laravel)
        }

        // Jika ProcessAssetDepreciation menampung Exception dengan benar secara individu dalam loppnya, 
        // AssetNormal tetap diproses. (Toleransi partial failure berfungsi)
        $countNormal1 = Depreciation::where('asset_id', $assetNormal1->id)->count();
        $countNormal2 = Depreciation::where('asset_id', $assetNormal2->id)->count();

        $this->assertGreaterThan(0, $countNormal1, 'Asset normal 1 gagal diproses akibat crash aset lain.');
        $this->assertGreaterThan(0, $countNormal2, 'Asset normal 2 gagal diproses akibat crash aset lain.');

        $countDefective = Depreciation::where('asset_id', $assetDefective->id)->count();
        $this->assertEquals(0, $countDefective, 'Asset defective seharusnya menghasilkan error log, tidak insert data ke DB.');
    }
}
