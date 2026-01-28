<?php

namespace Tests\Integration\Console;

use Tests\TestCase;
use App\Models\Company;
use App\Jobs\RunBulkDepreciation;
use Illuminate\Support\Facades\Bus;

class CalculateDepreciationTest extends TestCase
{
    /**
     * Test command dispatches jobs for all companies.
     */
    public function test_command_dispatches_jobs_for_all_companies(): void
    {
        Bus::fake();

        // Create 3 additional companies
        Company::factory()->count(3)->create();

        $expectedCount = Company::count();

        // Run Command
        $this->artisan('app:calculate-depreciation')
            ->assertExitCode(0);

        // Assert Job dispatched correct number of times
        Bus::assertDispatchedTimes(RunBulkDepreciation::class, $expectedCount);
    }

    /**
     * Test command handles empty database gracefully.
     */
    public function test_command_handles_no_companies(): void
    {
        Bus::fake();

        // Delete all companies
        Company::query()->delete();

        $this->artisan('app:calculate-depreciation')
            ->expectsOutput('No active companies found.')
            ->assertExitCode(0);

        Bus::assertNotDispatched(RunBulkDepreciation::class);
    }
}
