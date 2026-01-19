<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Company;
use App\Models\User;
use App\Models\Asset;
use App\Models\AssetClass;
use App\Models\AssetSubClass;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Depreciation;
use App\Scopes\CompanyScope;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Setup untuk setiap test
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable CompanyScope untuk testing
        Asset::withoutGlobalScope(CompanyScope::class);
        AssetClass::withoutGlobalScope(CompanyScope::class);
        AssetSubClass::withoutGlobalScope(CompanyScope::class);
        AssetName::withoutGlobalScope(CompanyScope::class);
        Location::withoutGlobalScope(CompanyScope::class);
        Department::withoutGlobalScope(CompanyScope::class);
        Depreciation::withoutGlobalScope(CompanyScope::class);
        
        // Create default company untuk testing
        $this->defaultCompany = Company::factory()->create([
            'name' => 'Test Company',
            'currency' => 'IDR',
        ]);
    }

    /**
     * Helper: Create authenticated user dengan company
     */
    protected function actingAsUser(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);
        $user->companies()->attach($this->defaultCompany->id);
        
        return $this->actingAs($user);
    }

    /**
     * Helper: Create company user dengan role
     */
    protected function createCompanyUser(Company $company = null, array $attributes = []): User
    {
        $company = $company ?? $this->defaultCompany;
        $user = User::factory()->create($attributes);
        $user->companies()->attach($company->id);
        
        return $user;
    }

    /**
     * Helper: Set active company di session
     */
    protected function setActiveCompany(Company $company = null): void
    {
        $company = $company ?? $this->defaultCompany;
        session(['active_company_id' => $company->id]);
    }

    /**
     * Helper: Assert JSON response struktur
     */
    protected function assertJsonStructure(array $structure): void
    {
        // Helper untuk validasi struktur JSON response
    }
}
