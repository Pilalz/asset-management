<?php

namespace Tests\System\Controllers;

use Tests\TestCase;
use App\Models\Location;
use App\Models\Asset;

class LocationControllerTest extends TestCase
{
    /**
     * Test location index
     */
    public function test_location_index(): void
    {
        $this->actingAsUser();

        $response = $this->get('/location');

        $response->assertStatus(200);
        $response->assertViewIs('location.index');
    }

    /**
     * Test dapat membuat location
     */
    public function test_can_create_location(): void
    {
        $this->actingAsUser();

        $response = $this->post('/location', [
                        'name' => 'Warehouse A',
                        'address' => 'Jl. Test',
                        'description' => 'Test Warehouse',
                        'company_id' => $this->company->id,
                    ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('locations', [
            'name' => 'Warehouse A',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test tidak bisa membuat location dengan data kosong
     */
    public function test_cannot_store_location_without_name(): void
    {
        $this->actingAsUser();
        
        $response = $this->post('/location', [
            'description' => 'Main Warehouse',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }

    /**
     * Gagal menghapus lokasi jika masih ada aset di dalamnya (Integritas Data).
     */
    public function test_cannot_delete_location_with_existing_assets(): void
    {
        $this->actingAsUser();
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        
        // Tautkan aset ke lokasi ini
        Asset::factory()->create([
            'asset_code' => 'AC-938532',
            'location_id' => $location->id,
            'company_id' => $this->company->id
        ]);

        $response = $this->delete("/location/{$location->id}");

        // Tergantung logic-mu, bisa redirect dengan error atau 403
        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('locations', ['id' => $location->id]);
    }
}
