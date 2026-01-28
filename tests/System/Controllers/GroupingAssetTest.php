<?php

namespace Tests\System\Controllers;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\AssetName;

class GroupingAssetTest extends TestCase
{
    // ===== ASSET CLASS CRUD =====

    /**
     * Test asset class index
     */
    public function test_asset_class_index(): void
    {
        $this->actingAsUser();

        $response = $this->get('/asset-class');

        $response->assertStatus(200);
        $response->assertViewIs('asset-class.index');
    }

    /**
     * Test dapat membuat asset class
     */
    public function test_can_store_asset_class(): void
    {
        $this->actingAsUser();
        
        $response = $this->withSession(['active_company_id' => $this->company->id])->post('/asset-class', [
            'name' => 'Fixed Assets',
            'obj_id' => 'FA-' . uniqid(),
            'obj_acc' => 'ACC-100',
            'company_id' => (string) $this->company->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('asset_classes', [
            'name' => 'Fixed Assets',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test tidak bisa membuat asset class tanpa name
     */
    public function test_cannot_store_asset_class_without_name(): void
    {
        $this->actingAsUser();
        
        $response = $this->post('/asset-class', [
            'obj_id' => 'FA-002',
            'obj_acc' => 'ACC-200',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    // ===== ASSET SUB CLASS CRUD =====

    /**
     * Test asset sub class index
     */
    public function test_asset_sub_class_index(): void
    {
        $this->actingAsUser();

        $response = $this->get('/asset-sub-class');

        $response->assertStatus(200);
        $response->assertViewIs('asset-sub-class.index');
    }

    /**
     * Test dapat membuat asset sub class
     */
    public function test_can_store_asset_sub_class(): void
    {
        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);
        
        $this->actingAsUser();
        
        $response = $this->post('/asset-sub-class', [
            'name' => 'Computers',
            'class_id' => (string) $assetClass->id,
            'company_id' => (string) $this->company->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('asset_sub_classes', [
            'name' => 'Computers',
            'class_id' => $assetClass->id,
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test tidak bisa membuat asset sub class tanpa name
     */
    public function test_cannot_store_asset_sub_class_without_name(): void
    {
        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);

        $this->actingAsUser();
        
        $response = $this->post('/asset-class', [
            'class_id' => (string) $assetClass->id,
            'company_id' => (string) $this->company->id,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    // ===== ASSET NAME CRUD =====

    /**
     * Test asset name index
     */
    public function test_asset_name_index(): void
    {
        $this->actingAsUser();

        $response = $this->get('/asset-name');

        $response->assertStatus(200);
        $response->assertViewIs('asset-name.index');
    }

    /**
     * Test dapat membuat asset name
     */
    public function test_can_store_asset_name(): void
    {
        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);

        $assetSubClass = AssetSubClass::factory()->create([
            'class_id' => $assetClass->id,
            'company_id' => $this->company->id
        ]);
        
        $this->actingAsUser();
        
        $response = $this->post('/asset-name', [
            'name' => 'Laptop',
            'sub_class_id' => $assetSubClass->id,
            'commercial' => 5,
            'fiscal' => 4,
            'grouping' => 'IT Equipment',
            'company_id' => (string) $this->company->id,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('asset_names', [
            'name' => 'Laptop',
            'sub_class_id' => $assetSubClass->id,
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test tidak bisa membuat asset name tanpa name
     */
    public function test_cannot_store_asset_name_without_name(): void
    {
        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);
        
        $assetSubClass = AssetSubClass::factory()->create([
            'class_id' => $assetClass->id,
            'company_id' => $this->company->id
        ]);
        
        $this->actingAsUser();
        
        $response = $this->post('/asset-class', [
            'sub_class_id' => $assetSubClass->id,
            'commercial' => 5,
            'fiscal' => 4,
            'grouping' => 'IT Equipment',
            'company_id' => (string) $this->company->id,
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    // ===== MODEL RELATIONSHIP TESTS =====

    /**
     * Test asset memiliki relasi dengan asset name
     */
    public function test_asset_has_asset_name_relationship(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $asset = Asset::factory()->create([
            'asset_name_id' => $assetName->id,
            'company_id' => $this->company->id,
        ]);

        $this->assertEquals($assetName->id, $asset->assetName->id);
    }

    /**
     * Test asset name memiliki relasi dengan asset class
     */
    public function test_asset_name_has_asset_class_hierarchy(): void
    {
        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);
        $assetSubClass = AssetSubClass::factory()->create([
            'class_id' => $assetClass->id,
            'company_id' => $this->company->id
        ]);
        $assetName = AssetName::factory()->create([
            'sub_class_id' => $assetSubClass->id,
            'company_id' => $this->company->id
        ]);

        $this->assertEquals($assetClass->id, $assetName->assetSubClass->assetClass->id);
    }
}
