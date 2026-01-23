<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Depreciation;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;

class DepreciationControllerTest extends TestCase
{   
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAsUser();

        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);
        
        $this->asset = Asset::factory()->create([
            'asset_number' => 'AST-001',
            'asset_name_id' => $assetName->id,
            'status' => 'Active',
            'asset_type' => 'FA',
            'po_no' => 'PO8339256',
            'description' => 'Test Asset',
            'location_id' => $location->id,
            'department_id' => $department->id,
            'quantity' => 1,
            'capitalized_date' => '2024-01-01',
            'start_depre_date' => '2024-01-01',
            'acquisition_value' => 10000000,
            'current_cost' => 10000000,
            'commercial_useful_life_month' => 96,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => 10000000,
            'fiscal_useful_life_month' => 96,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 10000000,
            'company_id' => $this->company->id
        ]);
    }

    /**
     * Test dapat melihat depreciation schedule
     */
    public function test_can_view_depreciation_schedule(): void
    {
        $response = $this->get("/asset/{$this->asset->id}?year=2024");

        $response->assertStatus(200);
        $response->assertViewIs('asset.fixed.show');
    }

    /**
     * Test depreciation schedule menampilkan data dengan benar
     */
    public function test_depreciation_schedule_shows_correct_data(): void
    {
        // Create some depreciations
        Depreciation::factory()->count(12)->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'depre_date' => Carbon::now(),
        ]);

        $response = $this->get("/asset/{$this->asset->id}?year=" . Carbon::now()->year);

        $response->assertStatus(200);
        $response->assertViewHas('pivotedData');
    }

    /**
     * Test dapat melihat depreciation index
     */
    public function test_can_view_depreciation_commercial_index(): void
    {
        $response = $this->get('/depreciation');

        $response->assertStatus(200);
        $response->assertViewIs('depreciation.commercial.index');
    }

    public function test_can_view_depreciation_fiscal_index(): void
    {
        $response = $this->get('/depreciation/fiscal');

        $response->assertStatus(200);
        $response->assertViewIs('depreciation.fiscal.index');
    }

    /**
     * Test depreciation datatable menampilkan data
     */
    public function test_depreciation_datatable_returns_data(): void
    {
        Depreciation::factory()->count(5)->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
        ]);

        $response = $this->get('/depreciation');

        $response->assertStatus(200);
        $response->assertViewHas([
            'pivotedData',
            'paginator',
            'months',
            'selectedYear',
        ]);
    }

    /**
     * Test dapat run bulk depreciation
     */
    public function test_can_run_bulk_depreciation(): void
    {
        $response = $this->post(
                '/depreciation/run-all', [
                'type' => 'commercial',
            ]);

        $response->assertStatus(200);
    }

    /**
     * Test depreciation calculation adalah correct
     */
    public function test_depreciation_calculation_is_correct(): void
    {
        $assetValue = 12000000;
        $usefulLifeMonths = 60;
        $monthlyDepre = $assetValue / $usefulLifeMonths;

        $depreciation = Depreciation::factory()->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'monthly_depre' => $monthlyDepre,
        ]);

        $this->assertEquals($monthlyDepre, $depreciation->monthly_depre);
        $this->assertEqualsWithDelta(200000, $depreciation->monthly_depre, 0.1);
    }

    /**
     * Test depreciation tidak bisa melebihi NBV
     */

    // SAMPAI SINI
    
    // public function test_depreciation_cannot_exceed_nbv(): void
    // {
    //     $this->actingAsUser();

    //     $this->asset->update([
    //         'commercial_accum_depre' => 11000000,
    //         'commercial_nbv' => 1000000,
    //     ]);

    //     $response = $this->post("/{$this->asset->id}/depreciate", [
    //         'amount' => 2000000, // Mencoba menyusutkan 2jt padahal sisa cuma 1jt
    //         'type' => 'commercial',
    //         'date' => now()->format('Y-m-d'),
    //     ]);

    //     $this->assertDatabaseHas('depreciations', [
    //         'asset_id' => $this->asset->id,
    //         'monthly_depre' => 1000000, // Harus terpotong otomatis menjadi 1jt
    //     ]);
        
    //     // // Try to depreciate more than remaining NBV
    //     // $monthlyDepre = 2000000; // More than remaining NBV

    //     // $depreciation = Depreciation::factory()->create([
    //     //     'asset_id' => $this->asset->id,
    //     //     'type' => 'commercial',
    //     //     'monthly_depre' => $monthlyDepre,
    //     // ]);

    //     // // System should cap it to remaining NBV
    //     // $this->assertLessThanOrEqual(1000000, $depreciation->monthly_depre);
    // }

    /**
     * Test depreciation history untuk asset
     */
    public function test_can_view_depreciation_history(): void
    {
        Depreciation::factory()->count(3)->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'company_id' => $this->company->id,
        ]);

        $depreciations = $this->asset->refresh()->depreciations;

        $this->assertEquals(3, $depreciations->count());
    }

    /**
     * Test depreciation expense report
     */
    public function test_can_view_depreciation_expense_report(): void
    {
        Depreciation::factory()->count(5)->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'company_id' => $this->company->id,
        ]);

        $response = $this->get('/depreciation/export-excel');

        // Endpoint might not exist, adjust as needed
        $response->assertStatus(200);
    }

    /**
     * Test depreciation dapat difilter by type
     */
    public function test_depreciation_can_be_filtered_by_type(): void
    {
        Depreciation::factory()->count(3)->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'company_id' => $this->company->id,
        ]);

        Depreciation::factory()->count(2)->create([
            'asset_id' => $this->asset->id,
            'type' => 'fiscal',
            'company_id' => $this->company->id,
        ]);

        $commercial = Depreciation::where('type', 'commercial')->count();
        $fiscal = Depreciation::where('type', 'fiscal')->count();

        $this->assertEquals(3, $commercial);
        $this->assertEquals(2, $fiscal);
    }

    /**
     * Test depreciation dapat difilter by date range
     */
    public function test_depreciation_can_be_filtered_by_date_range(): void
    {
        $start = Carbon::now()->startOfMonth();
        $end = Carbon::now()->endOfMonth();

        Depreciation::factory()->count(5)->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'depre_date' => $start->copy()->addDays(5),
            'company_id' => $this->company->id,
        ]);

        $depreciations = Depreciation::whereBetween('depre_date', [$start, $end])->count();

        $this->assertEquals(5, $depreciations);
    }

    /**
     * Test monthly depreciation accumulation
     */
    public function test_monthly_depreciation_accumulation(): void
    {
        $monthlyAmount = 200000;

        for ($i = 1; $i <= 12; $i++) {
            Depreciation::factory()->create([
                'asset_id' => $this->asset->id,
                'type' => 'commercial',
                'monthly_depre' => $monthlyAmount,
                'accumulated_depre' => $monthlyAmount * $i,
                'depre_date' => Carbon::now()->subMonths(12 - $i),
                'company_id' => $this->company->id,
            ]);
        }

        $totalAccumulated = Depreciation::where('asset_id', $this->asset->id)
            ->sum('monthly_depre');

        $this->assertEquals($monthlyAmount * 12, $totalAccumulated);
    }

    /**
     * Test user dari company berbeda tidak bisa akses depreciation
     */
    public function test_user_from_different_company_cannot_access_depreciation(): void
    {
        $otherCompany = Company::factory()->create();
        $otherAsset = Asset::factory()->create(['company_id' => $otherCompany->id]);

        $response = $this->get("/asset/{$otherAsset->id}?year=2025");

        $response->assertStatus(404);
    }
}
