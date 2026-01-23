<?php

namespace Tests\Feature;

use Tests\TestCase;

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
}
