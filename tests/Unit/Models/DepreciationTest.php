<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Depreciation;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Scopes\CompanyScope;

class DepreciationTest extends TestCase
{
    /**
     * Test model has correct fillable attributes.
     * This ensures mass assignment protection is configured properly.
     */
    public function test_model_has_correct_fillables(): void
    {
        $depreciation = new Depreciation();

        $expectedFillables = [
            'asset_id',
            'type',
            'depre_date',
            'monthly_depre',
            'accumulated_depre',
            'book_value',
            'company_id',
        ];

        $this->assertEquals($expectedFillables, $depreciation->getFillable());
    }

    /**
     * Test model uses correct database table name.
     */
    public function test_table_name_is_correct(): void
    {
        $model = new Depreciation();
        $this->assertEquals('depreciations', $model->getTable());
    }

    /**
     * Test model has timestamps enabled.
     */
    public function test_model_uses_timestamps(): void
    {
        $model = new Depreciation();
        $this->assertTrue($model->usesTimestamps());
    }

    /**
     * Test getAssetNameAttribute accessor returns asset number.
     */
    public function test_get_asset_name_attribute_returns_asset_number(): void
    {
        $this->actingAsUser();

        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_number' => 'AST-TEST-123',
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
        ]);

        $depreciation = Depreciation::factory()->create([
            'asset_id' => $asset->id,
            'company_id' => $this->company->id,
        ]);

        $result = $depreciation->asset_name;

        $this->assertEquals('AST-TEST-123', $result);
    }

    /**
     * Test getAssetNameAttribute returns 'unknown asset' for non-existent asset.
     */
    public function test_get_asset_name_attribute_returns_unknown_for_missing_asset(): void
    {
        $depreciation = new Depreciation([
            'asset_id' => 999999, // Non-existent
            'company_id' => 1,
        ]);

        $result = $depreciation->asset_name;

        $this->assertEquals('unknown asset', $result);
    }

    /**
     * Test model has relationships defined.
     */
    public function test_model_has_company_relationship(): void
    {
        $depreciation = new Depreciation();

        $this->assertTrue(method_exists($depreciation, 'company'));
    }

    /**
     * Test model has asset relationship defined.
     */
    public function test_model_has_asset_relationship(): void
    {
        $depreciation = new Depreciation();

        $this->assertTrue(method_exists($depreciation, 'asset'));
    }

    /**
     * Test struktur dasar model (Table, Fillable, Timestamps)
     */
    public function test_model_metadata_is_correct(): void
    {
        $model = new Depreciation();
        
        $this->assertEquals('depreciations', $model->getTable());
        $this->assertTrue($model->usesTimestamps());
        $this->assertContains('monthly_depre', $model->getFillable());
        $this->assertContains('company_id', $model->getFillable());
    }

    /**
     * Test model otomatis menerapkan CompanyScope (Multi-tenancy)
     */
    public function test_model_applies_company_scope(): void
    {
        $model = new Depreciation();
        $this->assertTrue($model->hasGlobalScope(new CompanyScope));
    }

    /**
     * Test Activity Log Options (Spatie Activity Log)
     */
    public function test_activity_log_options_are_configured(): void
    {
        $model = new Depreciation();
        $options = $model->getActivitylogOptions();

        $this->assertNotEmpty($options->logAttributes);
        $this->assertContains('monthly_depre', $options->logAttributes);
        $this->assertContains('book_value', $options->logAttributes);
    }

    /**
     * Test deskripsi activity log saat Depreciation dibuat.
     */
    public function test_depreciation_logs_activity_on_creation(): void
    {
        $this->actingAsUser();
        
        $asset = Asset::factory()->create(['asset_number' => 'LOG-001']);
        
        $depre = Depreciation::create([
            'asset_id' => $asset->id,
            'type' => 'commercial',
            'depre_date' => now(),
            'monthly_depre' => 1000,
            'accumulated_depre' => 1000,
            'book_value' => 9000,
            'company_id' => $this->company->id,
        ]);

        $this->assertDatabaseHas('activity_log', [
            'subject_id' => $depre->id,
            'description' => "Depreciation Asset 'LOG-001' has been created",
            'log_name' => (string) $this->company->id,
        ]);
    }

    /**
     * Test Accessor: asset_name
     */
    public function test_asset_name_accessor_logic(): void
    {
        $this->actingAsUser(); // Login via TestCase helper

        $asset = Asset::factory()->create(['asset_number' => 'AST-999']);
        $depre = new Depreciation(['asset_id' => $asset->id]);

        $this->assertEquals('AST-999', $depre->asset_name);
        
        // Test fallback
        $depreUnknown = new Depreciation(['asset_id' => 0]);
        $this->assertEquals('unknown asset', $depreUnknown->asset_name);
    }
}
