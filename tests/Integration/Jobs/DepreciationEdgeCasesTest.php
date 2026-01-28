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

class DepreciationEdgeCasesTest extends TestCase
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
     * Test assets with status 'Sold' are not depreciated.
     */
    public function test_sold_assets_are_not_depreciated(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
            'status' => 'Sold',
            'asset_type' => 'FA',
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 1000000,
            'acquisition_value' => 1000000,
            'current_cost' => 1000000,
            'start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        // No depreciation should be created
        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Test assets with status 'Disposal' are not depreciated.
     */
    public function test_disposal_assets_are_not_depreciated(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
            'status' => 'Disposal',
            'asset_type' => 'FA',
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 1000000,
            'acquisition_value' => 1000000,
            'current_cost' => 1000000,
            'start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }

    /**
     * Test assets with status 'Onboard' are not depreciated.
     */
    public function test_onboard_assets_are_not_depreciated(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
            'status' => 'Onboard',
            'asset_type' => 'FA',
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 1000000,
            'acquisition_value' => 1000000,
            'current_cost' => 1000000,
            'start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }


    /**
     * Test assets with future start_depre_date are not depreciated yet.
     */
    public function test_assets_with_future_start_date_are_not_depreciated(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
            'status' => 'Active',
            'asset_type' => 'FA',
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 1000000,
            'acquisition_value' => 1000000,
            'start_depre_date' => Carbon::now()->addMonths(3)->startOfMonth(), // Future date
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }


    /**
     * Test non-FA assets are not depreciated.
     */
    public function test_non_fa_assets_are_not_depreciated(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
            'status' => 'Active',
            'asset_type' => 'CA', // Current Asset, not Fixed Asset
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 1000000,
            'acquisition_value' => 1000000,
            'start_depre_date' => Carbon::now()->subMonths(5)->startOfMonth(),
        ]);

        Cache::forget('running-depreciation-process:' . $this->company->id);

        (new RunBulkDepreciation($this->company->id))->handle();

        $count = Depreciation::where('asset_id', $asset->id)->count();
        $this->assertEquals(0, $count);
    }
}
