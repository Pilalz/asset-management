<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Company;
use App\Models\User;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    protected Company $company;
    protected User $user;

    /**
     * Setup untuk setiap test
     */
    protected function setUp(): void
    {
        parent::setUp();
                
        // Create default company untuk testing
        $this->company = Company::factory()->create([
            'name' => 'Test Company',
            'currency' => 'IDR',
        ]);

        $this->user = User::factory()->create();
        $this->user->companies()->attach($this->company->id, ['role' => 'Asset Management']);
        
        session(['active_company_id' => $this->company->id]);
    }

    /**
     * Helper: Create authenticated user dengan company
     */
    protected function actingAsUser(string $role = 'Asset Management', array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->companies()->attach($this->company->id, ['role' => $role]);
        $user->update(['last_active_company_id' => $this->company->id]);

        session(['active_company_id' => $this->company->id]);
        $this->actingAs($user);

        return $user;
    }
}
