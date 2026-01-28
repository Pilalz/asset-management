<?php

namespace Tests\Integration\Jobs;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Depreciation;
use App\Jobs\RunBulkDepreciation;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

class DepreciationJobTest extends TestCase
{
    /**
     * Setup test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();
    }

    /**
     * Create a test asset with default or custom attributes.
     *
     * @param array $overrides Custom attributes to override defaults
     * @return Asset
     */
    protected function createAsset(array $overrides = []): Asset
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        return Asset::factory()->create(array_merge([
            'asset_number' => 'AST-TEST-JOB',
            'asset_name_id' => $assetName->id,
            'status' => 'Active',
            'asset_type' => 'FA',
            'company_id' => $this->company->id,
            'acquisition_value' => 12000000, // 12 Million
            'current_cost' => 12000000,

            // Commercial
            'commercial_useful_life_month' => 12,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => 12000000,

            // Fiscal
            'fiscal_useful_life_month' => 12,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 12000000,

            'start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth(),
            'location_id' => $location->id,
            'department_id' => $department->id,
        ], $overrides));
    }

    /**
     * Test job calculates commercial depreciation correctly.
     */
    public function test_job_calculates_depreciation_correctly(): void
    {
        $asset = $this->createAsset();

        // Expected Monthly Depreciation = 12,000,000 / 12 = 1,000,000
        $expectedMonthly = 1000000;

        // Clear cache locks before running job
        Cache::forget('running-depreciation-process:' . $this->company->id);

        $job = new RunBulkDepreciation($this->company->id);
        $job->handle();

        // Assert records were created
        $count = Depreciation::where('asset_id', $asset->id)
            ->where('type', 'commercial')
            ->count();
        $this->assertGreaterThanOrEqual(1, $count);

        // Assert calculation is correct
        $firstDepre = Depreciation::where('asset_id', $asset->id)
            ->where('type', 'commercial')
            ->orderBy('depre_date', 'asc')
            ->first();

        $this->assertEquals($expectedMonthly, $firstDepre->monthly_depre);

        // Assert asset master data is updated correctly
        $asset->refresh();
        $this->assertLessThan(12000000, $asset->commercial_nbv);
        $this->assertGreaterThanOrEqual(0, $asset->commercial_nbv);
        $this->assertEquals($asset->acquisition_value - ($count * $expectedMonthly), $asset->commercial_nbv);
        $this->assertEquals(($count * $expectedMonthly), $asset->commercial_accum_depre);
    }

    /**
     * Test job calculates fiscal depreciation correctly.
     */
    public function test_job_calculates_fiscal_depreciation_correctly(): void
    {
        $asset = $this->createAsset();

        $expectedMonthly = 1000000;

        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        // Assert fiscal depreciation records were created
        $count = Depreciation::where('asset_id', $asset->id)
            ->where('type', 'fiscal')
            ->count();
        $this->assertGreaterThanOrEqual(1, $count);

        // Assert fiscal calculation is correct
        $firstDepre = Depreciation::where('asset_id', $asset->id)
            ->where('type', 'fiscal')
            ->orderBy('depre_date', 'asc')
            ->first();

        $this->assertEquals($expectedMonthly, $firstDepre->monthly_depre);

        // Assert fiscal asset data is updated
        $asset->refresh();
        $this->assertLessThan(12000000, $asset->fiscal_nbv);
        $this->assertGreaterThanOrEqual(0, $asset->fiscal_nbv);
    }

    /**
     * Test job handles idempotency - running twice should not duplicate records.
     */
    public function test_job_handles_idempotency(): void
    {
        $asset = $this->createAsset();

        Cache::forget('running-depreciation-process:' . $this->company->id);

        // Run job first time
        (new RunBulkDepreciation($this->company->id))->handle();
        $countAfterFirstRun = Depreciation::where('asset_id', $asset->id)->count();

        // Run job second time
        (new RunBulkDepreciation($this->company->id))->handle();
        $countAfterSecondRun = Depreciation::where('asset_id', $asset->id)->count();

        // Count should remain the same
        $this->assertEquals($countAfterFirstRun, $countAfterSecondRun);
    }

    /**
     * Test job stops depreciation when asset is fully depreciated.
     */
    public function test_job_stops_when_fully_depreciated(): void
    {
        Cache::forget('running-depreciation-process:' . $this->company->id);

        // Create asset that started 20 months ago (should be fully depreciated)
        $asset = $this->createAsset([
            'commercial_useful_life_month' => 12,
            'start_depre_date' => Carbon::now()->subMonths(20)->startOfMonth(),
        ]);

        (new RunBulkDepreciation($this->company->id))->handle();

        $asset->refresh();

        // Asset should be fully depreciated
        $this->assertEquals(0, $asset->commercial_nbv);
        $this->assertEquals($asset->acquisition_value, $asset->commercial_accum_depre);
    }

    /**
     * Test job gagal jalan jika perusahaan yang sama sedang dalam proses penyusutan (Locking).
     */
    public function test_job_respects_concurrency_lock(): void
    {
        $lockKey = 'running-depreciation-process:' . $this->company->id;
        
        // 1. Ambil gembok secara manual
        $lock = Cache::lock($lockKey, 60);
        $lock->get();

        $job = new RunBulkDepreciation($this->company->id);
        
        // 2. Jalankan handle, seharusnya dia tidak membuat record baru karena gagal block()
        $job->handle();

        // 3. Pastikan tidak ada data penyusutan yang terbuat
        $this->assertEquals(0, Depreciation::count());
        
        $lock->release();
    }

    /**
     * Test nilai penyusutan dipotong (capped) agar NBV tidak negatif.
     */
    public function test_depreciation_is_capped_at_remaining_nbv(): void
    {
        // Skenario: Sisa NBV tinggal 500.000, tapi penyusutan bulanan seharusnya 1.000.000
        $asset = $this->createAsset([
            'acquisition_value' => 12000000,
            'commercial_nbv' => 500000, // Sisa dikit
            'commercial_useful_life_month' => 12, // Bulanan harusnya 1jt
            'start_depre_date' => Carbon::now()->subMonth()->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);
        (new RunBulkDepreciation($this->company->id))->handle();

        $latestDepre = Depreciation::where('asset_id', $asset->id)->first();
        
        // Harus dipotong jadi 500rb, bukan 1jt
        $this->assertEquals(500000, $latestDepre->monthly_depre);
        $this->assertEquals(0, $asset->refresh()->commercial_nbv);
    }

    /**
     * Test akurasi rumus monthly depre dengan pembulatan (round).
     */
    public function test_monthly_depreciation_formula_accuracy(): void
    {
        // Aset 10jt, umur 36 bulan -> 10.000.000 / 36 = 277.777,77... -> round = 277.778
        $asset = $this->createAsset([
            'acquisition_value' => 10000000,
            'commercial_useful_life_month' => 36,
            'commercial_nbv' => 10000000,
            'start_depre_date' => now()->subMonth()->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);
        (new RunBulkDepreciation($this->company->id))->handle();

        $depre = Depreciation::where('asset_id', $asset->id)->first();
        
        // Verifikasi hasil pembulatan ke atas sesuai fungsi round() PHP
        $this->assertEquals(277778, (int)$depre->monthly_depre);
    }

    /**
     * Test atomisitas: Data tidak boleh berubah jika terjadi error di tengah proses.
     */
    public function test_depreciation_rollback_on_failure(): void
    {
        $this->actingAsUser();
        $asset = $this->createAsset(['commercial_nbv' => 5000000]);
        $originalNbv = $asset->commercial_nbv;

        Cache::forget('running-depreciation-process:' . $this->company->id);

        // Mocking database untuk melempar Exception saat create Depreciation
        \Illuminate\Support\Facades\DB::shouldReceive('transaction')
            ->andThrow(new \Exception('Simulated Database Failure'));

        try {
            (new RunBulkDepreciation($this->company->id))->handle();
        } catch (\Exception $e) {
            // Abaikan exception simulasi
        }

        // Pastikan record Depreciation tidak masuk
        $this->assertEquals(0, \App\Models\Depreciation::where('asset_id', $asset->id)->count());
        
        // Pastikan NBV aset tidak berubah (Rollback sukses)
        $this->assertEquals($originalNbv, $asset->refresh()->commercial_nbv);
    }
}
