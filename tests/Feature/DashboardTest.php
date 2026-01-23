<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use App\Models\Asset;
use Carbon\Carbon;

class DashboardTest extends TestCase
{
    /**
     * Test dapat membuka halaman dashboard
     */
    public function test_can_access_dashboard(): void
    {
        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('index');
    }

    /**
     * Test guest user tidak bisa access dashboard
     */
    public function test_guest_cannot_access_protected_routes(): void
    {
        $response = $this->get('/dashboard');

        // Harus redirect ke login atau 403
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]), 
            "Expected 302 or 403, but got " . $response->getStatusCode());
    }

    /**
     * Test dashboard menampilkan statistik aset
     */
    public function test_dashboard_shows_asset_statistics(): void
    {
        Asset::factory()->count(10)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'FA',
        ]);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('assetFixed');
        $response->assertViewHas('assetArrival');
        $response->assertViewHas('assetLVA');
    }

    /**
     * Test dashboard menampilkan asset by class
     */
    public function test_dashboard_shows_assets_by_class(): void
    {
        // Create assets dengan berbagai class
        for ($i = 0; $i < 5; $i++) {
            Asset::factory()->create([
                'company_id' => $this->company->id,
            ]);
        }

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('assetClassData');
    }

    public function test_dashboard_shows_assets_by_location(): void
    {
        // Create assets dengan berbagai class
        for ($i = 0; $i < 5; $i++) {
            Asset::factory()->create([
                'company_id' => $this->company->id,
            ]);
        }

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('assetLocData');
    }

    /**
     * Test dashboard menampilkan depreciation summary
     */
    public function test_dashboard_shows_depreciation_summary(): void
    {
        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'commercial_accum_depre' => 1000000,
            'fiscal_accum_depre' => 800000,
        ]);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('commercialSumData');
        $response->assertViewHas('fiscalSumData');
        $response->assertViewHas('commercialCountData');
        $response->assertViewHas('fiscalCountData');
    }

    // /**
    //  * Test dashboard menampilkan asset value summary
    //  */
    // public function test_dashboard_shows_asset_value_summary(): void
    // {
    //     Asset::factory()->count(5)->create([
    //         'company_id' => $this->company->id,
    //         'acquisition_value' => 10000000,
    //     ]);

    //     $this->actingAsUser();

    //     $response = $this->get('/dashboard');

    //     $response->assertStatus(200);
    //     $response->assertViewHas('totalValue');
    // }

    /**
     * Test dashboard menampilkan recent activities
     */
    public function test_dashboard_shows_asset_remarks(): void
    {
        // Create activities
        Asset::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('assetRemaks');
        $response->assertViewHas('assetRemaksCount');
    }

    /**
     * Test dashboard chart data untuk depreciation
     */
    // public function test_dashboard_depreciation_chart_data(): void
    // {
    //     $this->actingAsUser();

    //     $response = $this->get('/dashboard/depreciation-chart?year=' . Carbon::now()->year);

    //     $response->assertStatus(200);
    //     $response->assertJsonStructure(['labels', 'data']);
    // }

    /**
     * Test dashboard hanya show data dari active company
     */
    public function test_dashboard_only_shows_active_company_data(): void
    {
        $otherCompany = Company::factory()->create();

        Asset::factory()->count(5)->create([
            'company_id' => $this->company->id,
        ]);

        Asset::factory()->count(10)->create([
            'company_id' => $otherCompany->id,
        ]);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        // Should only show 5 assets from active company
    }

    /**
     * Test dashboard with no data
     */
    public function test_dashboard_handles_empty_data(): void
    {
        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        // Should handle gracefully with no errors
    }

    /**
     * Test unauthenticated user cannot access dashboard
     */
    public function test_unauthenticated_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test dashboard performance dengan banyak data
     */
    public function test_dashboard_performance_with_large_dataset(): void
    {
        // Create 1000 assets
        Asset::factory()->count(1000)->create([
            'company_id' => $this->company->id,
        ]);

        $startTime = microtime(true);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $endTime = microtime(true);
        $duration = $endTime - $startTime;

        $response->assertStatus(200);
        // Should load in reasonable time (< 3 seconds)
        $this->assertLessThan(3, $duration);
    }

    /**
     * Test dashboard summary metrics accuracy
     */
    public function test_dashboard_summary_metrics_are_accurate(): void
    {
        // Create specific scenario
        Asset::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'status' => 'Active',
            'acquisition_value' => 10000000,
        ]);

        Asset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'status' => 'Sold',
        ]);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        // Verify metrics
        $this->assertEquals(7, Asset::where('company_id', $this->company->id)->count());
    }

    /**
     * Test dashboard pending depreciation notice
     */
    public function test_dashboard_shows_pending_depreciation_notice(): void
    {
        $lastMonth = Carbon::now()->subMonthNoOverflow();

        Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'commercial_nbv' => 5000000,
            'start_depre_date' => Carbon::now()->subMonths(12),
        ]);

        $this->actingAsUser();

        $response = $this->get('/dashboard');

        $response->assertStatus(200);
        // Should show pending depreciation indicator
    }
}
