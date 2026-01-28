<?php

namespace Tests\System\Controllers;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\User;
use App\Models\Company;

class AssetControllerTest extends TestCase
{
    /**
     * Test dapat membuka halaman index asset
     */
    public function test_asset_index_page_loads(): void
    {
        $this->actingAsUser();
        
        $response = $this->get('/asset');

        $response->assertStatus(200);
        $response->assertViewIs('asset.fixed.index');
    }

    /**
     * Test unauthorized user tidak bisa akses asset index
     */
    public function test_unauthenticated_user_cannot_access_asset_index(): void
    {
        $response = $this->get('/asset');

        $response->assertRedirect('/login');
    }

    /**
     * Test asset index menampilkan daftar asset
     */
    public function test_asset_index_shows_assets(): void
    {
        $this->actingAsUser();

        $assets = Asset::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        $response = $this->get('/asset');

        $response->assertStatus(200);
    }

    /**
     * Test dapat membuka halaman create asset
     */
    public function test_asset_create_page_loads(): void
    {
        $this->actingAsUser();

        $response = $this->get('/asset/create');

        $response->assertStatus(200);
        $response->assertViewIs('asset.fixed.create');
    }

    /**
     * Test dapat membuat asset baru dengan valid data
     */
    public function test_can_store_asset_with_valid_data(): void
    {        
        $this->withoutExceptionHandling();

        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $data = [
            'asset_number' => 'AST-001',
            'asset_code' => 'AC-42837',
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
            'company_id' => $this->company->id,
        ];

        $this->actingAsUser();

        $response = $this->post('/asset', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('assets', [
            'asset_number' => 'AST-001',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test user dengan role user bisa create asset
     */
    public function test_user_role_can_create_asset(): void
    {
        // User dengan role 'Asset Management' - bisa create
        $userUser = User::factory()->create(['last_active_company_id' => $this->company->id]);
        $userUser->companies()->attach($this->company->id, ['role' => 'Asset Management']);
        
        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);
        $assetSubClass = AssetSubClass::factory()->create([
            'class_id' => $assetClass->id,
            'company_id' => $this->company->id
        ]);
        $assetName = AssetName::factory()->create([
            'sub_class_id' => $assetSubClass->id,
            'company_id' => $this->company->id
        ]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);
        
        $this->actingAs($userUser);
        
        $response = $this->post('/asset', [
            'asset_number' => 'AST-2024-003',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'Test asset',
            'location_id' => $location->id,
            'department_id' => $department->id,
            'quantity' => 1,
            'capitalized_date' => '2024-01-01',
            'acquisition_value' => 10000000,
            'current_cost' => 10000000,
            'commercial_useful_life_month' => 60,
            'fiscal_useful_life_month' => 48,
        ]);

        // Harus 302 (redirect after success)
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Test user dengan role viewer tidak bisa create asset
     */
    public function test_viewer_cannot_create_asset(): void
    {
        // User tanpa role 'Asset Management' - tidak bisa akses create
        $viewerUser = User::factory()->create(['last_active_company_id' => $this->company->id]);
        $viewerUser->companies()->attach($this->company->id, ['role' => 'Viewer']);
        
        $this->actingAs($viewerUser);
        
        $response = $this->post('/asset', [
            'asset_number' => 'AST-TEST',
        ]);

        // Harus 403 Forbidden atau redirect
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403]));
    }

    /**
     * Test asset number harus unique per company
     */
    public function test_asset_number_must_be_unique_per_company(): void
    {
        $this->actingAsUser();

        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $existingAsset = Asset::factory()->create([
            'asset_number' => 'AST-DUP-001',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'Test asset',
            'location_id' => $location->id,
            'department_id' => $department->id,
            'quantity' => 1,
            'capitalized_date' => '2024-01-01',
            'acquisition_value' => 10000000,
            'current_cost' => 10000000,
            'commercial_useful_life_month' => 60,
            'fiscal_useful_life_month' => 48,
            'company_id' => $this->company->id,
        ]);

        $response = $this->post('/asset', [
            'asset_number' => 'AST-DUP-001', // Sama dengan existing
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'description' => 'Test asset',
            'location_id' => $location->id,
            'department_id' => $department->id,
            'quantity' => 1,
            'capitalized_date' => '2024-01-01',
            'acquisition_value' => 10000000,
            'current_cost' => 10000000,
            'commercial_useful_life_month' => 60,
            'fiscal_useful_life_month' => 48,
            'status' => 'Active',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /**
     * Test tidak bisa membuat asset dengan data tidak valid
     */
    public function test_cannot_store_asset_with_invalid_data(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $this->actingAsUser();

        $response = $this->post('/asset', [
                'asset_number' => '', // Required
                'acquisition_value' => 'invalid', // Should be numeric
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
                'current_cost' => 10000000,
                'commercial_useful_life_month' => 96,
                'commercial_accum_depre' => 0,
                'commercial_nbv' => 10000000,
                'fiscal_useful_life_month' => 96,
                'fiscal_accum_depre' => 0,
                'fiscal_nbv' => 10000000,
                'company_id' => $this->company->id,
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test dapat melihat detail asset
     */
    public function test_can_view_asset_detail(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
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

        $this->actingAsUser();

        $response = $this->get("/asset/{$asset->id}");

        $response->assertStatus(200);
    }

    /**
     * Test dapat membuka halaman edit asset
     */
    public function test_asset_edit_page_loads(): void
    {
        $asset = Asset::factory()->create(['company_id' => $this->company->id]);

        $this->actingAsUser();

        $response = $this->get("/asset/{$asset->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('asset.fixed.edit');
        $response->assertViewHas('asset', $asset);
    }

    /**
     * Test dapat update asset dengan valid data
     */
    public function test_can_update_asset_with_valid_data(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
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

        $data = [
            'asset_number' => 'AST-UPDATED',
            'description' => 'Updated Description',
            'status' => 'Inactive',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'po_no' => 'PO8339256',
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
        ];

        $this->actingAsUser();

        $response = $this->put("/asset/{$asset->id}", $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'asset_number' => 'AST-UPDATED',
            'description' => 'Updated Description',
        ]);
    }

    /**
     * Test tidak bisa update dengan data invalid
     */
    public function test_cannot_update_asset_with_invalid_data(): void
    {
        $asset = Asset::factory()->create(['company_id' => $this->company->id]);

        $this->actingAsUser();

        $response = $this->put(
                "/asset/{$asset->id}", [
                    'acquisition_value' => 'not-a-number',
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test dapat delete asset
     */
    public function test_can_delete_asset(): void
    {
        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
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

        $this->actingAsUser();

        $response = $this->delete("/asset/{$asset->id}");

        $response->assertRedirect();
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);
    }

    /**
     * Test datatable API menampilkan assets dengan pagination
     */
    public function test_asset_datatable_api_returns_data(): void
    {
        $this->withoutExceptionHandling();
        $this->actingAsUser();

        Asset::factory()->count(15)->create(['company_id' => $this->company->id]);

        $response = $this->get('/api/asset');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'recordsTotal',
            'recordsFiltered',
            'data',
        ]);
    }

    /**
     * Test datatable dapat disort dan difilter
     */
    public function test_asset_datatable_can_be_sorted_and_filtered(): void
    {
        Asset::factory()->count(5)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'FA',
        ]);

        Asset::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'LVA',
        ]);

        $this->actingAsUser();

        $response = $this->get('/api/asset?search[value]=FA');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data']);
    }

    /**
     * Test user dari company berbeda tidak bisa akses asset
     */
    public function test_user_from_different_company_cannot_access_asset(): void
    {
        $otherCompany = Company::factory()->create();
        $asset = Asset::factory()->create(['company_id' => $otherCompany->id]);

        $this->actingAsUser();
        // User dari company1 mencoba akses asset dari company2
        $response = $this->get("/asset/{$asset->id}");

        // Should return 404 atau redirect, tergantung CompanyScope implementation
        $response->assertStatus(404);
    }

    // ===== MULTI-TENANCY TESTS =====

    /**
     * Test user dari company berbeda tidak bisa akses asset
     */
    public function test_user_from_different_company_cannot_access_asset2(): void
    {
        $otherCompany = Company::factory()->create();
        $otherUser = User::factory()->create();
        $otherUser->companies()->attach($otherCompany->id, ['role' => 'admin']);

        $assetClass = AssetClass::factory()->create(['company_id' => $this->company->id]);
        $assetSubClass = AssetSubClass::factory()->create(['class_id' => $assetClass->id, 'company_id' => $this->company->id]);
        $assetName = AssetName::factory()->create(['sub_class_id' => $assetSubClass->id, 'company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $asset = Asset::factory()->create([
            'asset_name_id' => $assetName->id,
            'company_id' => $this->company->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
        ]);

        $this->actingAsUser();
        // Try to access with different company user (testing multi-tenancy)
        // May succeed if CompanyScope allows viewing, so just check it doesn't throw error
        $response = $this->session(['active_company_id' => $otherCompany->id])->get("/asset/{$asset->id}");

        // Asset dari company lain mungkin tidak terlihat (404) atau disembunyikan (403)
        $response->assertStatus(404);
    }
}
