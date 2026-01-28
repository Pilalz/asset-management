<?php

namespace Tests\System\Auth;

use Tests\TestCase;
use App\Models\User;

class DepreciationAuthorizationTest extends TestCase
{
    /**
     * Test non-admin user cannot run bulk depreciation.
     */
    public function test_non_admin_cannot_run_bulk_depreciation(): void
    {
        // Create regular user (non-admin) - using 'Viewer' role which is NOT admin
        $user = User::factory()->create();
        $user->companies()->attach($this->company->id, ['role' => 'Viewer']);
        $user->update(['last_active_company_id' => $this->company->id]);

        $this->actingAs($user);
        session(['active_company_id' => $this->company->id]);

        $response = $this->post('/depreciation/run-all', [
            'type' => 'commercial',
        ]);

        // Debug: See actual status
        $actualStatus = $response->status();

        // Should be forbidden (403) or redirect (302)
        // If this fails, the actual status is: {$actualStatus}
        $this->assertContains(
            $actualStatus,
            [403, 302],
            "Expected 403 or 302, but got {$actualStatus}"
        );
    }

    /**
     * Test admin user can run bulk depreciation.
     */
    public function test_admin_can_run_bulk_depreciation(): void
    {
        // Admin role is set in TestCase actingAsUser()
        $this->actingAsUser();

        $response = $this->post('/depreciation/run-all', [
            'type' => 'commercial',
        ]);

        $response->assertStatus(200);
    }

    /**
     * Test authenticated non-admin user CAN export commercial depreciation reports.
     * Business Rule: All authenticated company users can export, not just admins.
     */
    public function test_authenticated_user_can_export_commercial_depreciation(): void
    {
        $user = User::factory()->create();
        $user->companies()->attach($this->company->id, ['role' => 'Asset Management']);
        $user->update(['last_active_company_id' => $this->company->id]);

        $this->actingAs($user);
        session(['active_company_id' => $this->company->id]);

        $response = $this->get(route('commercial.export', ['year' => now()->year]));

        // Should succeed - all authenticated company users can export
        $response->assertStatus(200);
    }

    /**
     * Test authenticated non-admin user CAN export fiscal depreciation reports.
     * Business Rule: All authenticated company users can export, not just admins.
     */
    public function test_authenticated_user_can_export_fiscal_depreciation(): void
    {
        $user = User::factory()->create();
        $user->companies()->attach($this->company->id, ['role' => 'Asset Management']);
        $user->update(['last_active_company_id' => $this->company->id]);

        $this->actingAs($user);
        session(['active_company_id' => $this->company->id]);

        $response = $this->get(route('fiscal.export', ['year' => now()->year]));

        // Should succeed - all authenticated company users can export
        $response->assertStatus(200);
    }

    /**
     * Test unauthenticated guest cannot access depreciation pages.
     */
    public function test_guest_cannot_access_depreciation_index(): void
    {
        // Not authenticated
        $response = $this->get('/depreciation');

        $response->assertRedirect('/login');
    }

    /**
     * Test unauthenticated guest cannot access fiscal depreciation pages.
     */
    public function test_guest_cannot_access_fiscal_depreciation_index(): void
    {
        $response = $this->get('/depreciation/fiscal');

        $response->assertRedirect('/login');
    }

    /**
     * Test unauthenticated guest cannot export commercial depreciation.
     */
    public function test_guest_cannot_export_commercial_depreciation(): void
    {
        // Not authenticated
        $response = $this->get(route('commercial.export', ['year' => now()->year]));

        $response->assertRedirect('/login');
    }

    /**
     * Test unauthenticated guest cannot export fiscal depreciation.
     */
    public function test_guest_cannot_export_fiscal_depreciation(): void
    {
        // Not authenticated
        $response = $this->get(route('fiscal.export', ['year' => now()->year]));

        $response->assertRedirect('/login');
    }
}
