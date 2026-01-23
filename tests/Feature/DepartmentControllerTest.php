<?php

namespace Tests\Feature;

use Tests\TestCase;

class DepartmentControllerTest extends TestCase
{
    /**
     * Test department index
     */
    public function test_department_index(): void
    {
        $this->actingAsUser();

        $response = $this->get('/department');

        $response->assertStatus(200);
        $response->assertViewIs('department.index');
    }

    /**
     * Test dapat membuat department
     */
    public function test_can_create_department(): void
    {
        $this->actingAsUser();

        $response = $this->post('/department', [
                        'name' => 'IT',
                        'description' => 'IT Department',
                        'company_id' => $this->company->id,
                    ]);

        $response->assertStatus(302);
        $this->assertDatabaseHas('departments', [
            'name' => 'IT',
            'company_id' => $this->company->id,
        ]);
    }

    /**
     * Test tidak bisa membuat department tanpa name
     */
    public function test_cannot_store_department_without_name(): void
    {
        $this->actingAsUser();
        
        $response = $this->post('/department', [
            'description' => 'Information Technology',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
