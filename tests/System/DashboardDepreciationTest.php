<?php

namespace Tests\System\Controllers;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Depreciation;
use Carbon\Carbon;

/**
 * Pengujian komponen Dashboard khusus bagian Kalkulasi Depresiasi.
 * Memastikan widget pada halaman Dashboard menampilkan angka yang akurat sesuai data riil.
 */
class DashboardDepreciationTest extends TestCase
{
    /** @var AssetName */
    protected AssetName $assetName;

    /** @var Location */
    protected Location $location;

    /** @var Department */
    protected Department $department;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();

        $this->assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $this->location = Location::factory()->create(['company_id' => $this->company->id]);
        $this->department = Department::factory()->create(['company_id' => $this->company->id]);
    }

    private function createAsset(array $overrides = []): Asset
    {
        return Asset::factory()->create(array_merge([
            'company_id' => $this->company->id,
            'asset_name_id' => $this->assetName->id,
            'location_id' => $this->location->id,
            'department_id' => $this->department->id,
            'status' => 'Active',
            'asset_type' => 'FA',
            'commercial_nbv' => 10_000_000,
        ], $overrides));
    }

    /**
     * Memastikan halaman Dashboard dapat diakses oleh user yang login.
     */
    public function test_dashboard_is_accessible(): void
    {
        $response = $this->get('/dashboard');
        $response->assertStatus(200);
        $response->assertViewIs('index');
    }

    /**
     * Memastikan widget "Total Asset Value" menampilkan total keseluruhan commercial_nbv yang ada (kecuali status Sold/Disposal).
     */
    public function test_dashboard_calculates_total_asset_value_from_nbv_correctly(): void
    {
        // 2 Aset Aktif -> Total NBV = 25.000.000
        $this->createAsset(['commercial_nbv' => 15000000]);
        $this->createAsset(['commercial_nbv' => 10000000]);

        // Aset Sold (Harus diabaikan oleh dashboard)
        $this->createAsset(['commercial_nbv' => 5000000, 'status' => 'Sold']);

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $totalAssetPrice = $response->viewData('totalAssetPrice');

        $this->assertEquals(25000000, $totalAssetPrice);
    }

    /**
     * Memastikan chart depresiasi per bulan menampilkan kalkulasi sum dan count record yang terkelompok per tanggal (depre_date).
     */
    public function test_dashboard_chart_aggregates_monthly_depreciation_data(): void
    {
        $asset1 = $this->createAsset();
        $asset2 = $this->createAsset();

        $dateStr = Carbon::now()->endOfMonth()->toDateString();
        $lastMonthStr = Carbon::now()->subMonth()->endOfMonth()->toDateString();

        // Bulan berjalan: 2 record commercial
        Depreciation::factory()->create(['asset_id' => $asset1->id, 'type' => 'commercial', 'depre_date' => $dateStr, 'monthly_depre' => 1000000, 'company_id' => $this->company->id]);
        Depreciation::factory()->create(['asset_id' => $asset2->id, 'type' => 'commercial', 'depre_date' => $dateStr, 'monthly_depre' => 2000000, 'company_id' => $this->company->id]);

        // Bulan lalu: 1 record commercial
        Depreciation::factory()->create(['asset_id' => $asset1->id, 'type' => 'commercial', 'depre_date' => $lastMonthStr, 'monthly_depre' => 500000, 'company_id' => $this->company->id]);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $chartLabels = collect($response->viewData('chartLabels'))->toArray();
        $commercialSumList = collect($response->viewData('commercialSumData'))->toArray();
        $commercialCountList = collect($response->viewData('commercialCountData'))->toArray();

        // Pastikan ada 2 label/titik (bulan lalu & bulan ini) di chart
        $this->assertCount(2, $chartLabels);
        $this->assertTrue(in_array($lastMonthStr, $chartLabels));
        $this->assertTrue(in_array($dateStr, $chartLabels));

        // Karena diurutkan ASC, index 0 adalah lastMonthStr, index 1 adalah dateStr
        $this->assertEquals(500000, $commercialSumList[0] ?? 0); // 1 Aset = 500k
        $this->assertEquals(1, $commercialCountList[0] ?? 0);

        $this->assertEquals(3000000, $commercialSumList[1] ?? 0); // 1jt + 2jt = 3jt
        $this->assertEquals(2, $commercialCountList[1] ?? 0);
    }

    /**
     * Memastikan widget informasi "Aset yang disusutkan bulan ini" menampilkan jumlah count tepat pada data riwayat terakhir.
     */
    public function test_dashboard_calculates_current_month_depreciated_count(): void
    {
        $asset = $this->createAsset();

        // Cukup insert 1
        Depreciation::factory()->create([
            'asset_id' => $asset->id,
            'type' => 'commercial',
            'depre_date' => Carbon::now()->endOfMonth()->toDateString(),
            'company_id' => $this->company->id
        ]);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);

        $currentMonthDepreCount = $response->viewData('currentMonthDepreCount');

        $this->assertEquals(1, $currentMonthDepreCount);
    }
}
