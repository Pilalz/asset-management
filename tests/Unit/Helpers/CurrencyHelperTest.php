<?php

namespace Tests\Unit\Helpers;

use Tests\TestCase;
use App\Models\Company;
use Illuminate\Support\Facades\Cache;

class CurrencyHelperTest extends TestCase
{
    /**
     * Test format_currency dengan session company
     */
    public function test_format_currency_with_active_company(): void
    {
        $company = Company::factory()->create(['currency' => 'IDR']);
        session(['active_company_id' => $company->id]);

        $result = format_currency(1000000);

        // Should return formatted Rp currency
        $this->assertStringContainsString('Rp', $result);
        $this->assertStringContainsString('1', $result);
    }

    /**
     * Test format_currency dengan USD
     */
    public function test_format_currency_with_usd(): void
    {
        $company = Company::factory()->create(['currency' => 'USD']);
        session(['active_company_id' => $company->id]);

        $result = format_currency(1000);

        // Should return formatted USD currency
        $this->assertStringContainsString('$', $result);
    }

    /**
     * Test format_currency tanpa active company (fallback ke IDR)
     */
    public function test_format_currency_without_active_company(): void
    {
        session()->forget('active_company_id');

        $result = format_currency(500000);

        // Should fallback to Rp
        $this->assertStringContainsString('Rp', $result);
        $this->assertStringContainsString('500', $result);
    }

    /**
     * Test format_currency dengan nilai decimal
     */
    public function test_format_currency_with_decimal(): void
    {
        $company = Company::factory()->create(['currency' => 'IDR']);
        session(['active_company_id' => $company->id]);

        $result = format_currency(1000000.50);

        // Should format dengan benar
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('Rp', $result);
    }

    /**
     * Test format_currency dengan zero value
     */
    public function test_format_currency_with_zero(): void
    {
        $company = Company::factory()->create(['currency' => 'IDR']);
        session(['active_company_id' => $company->id]);

        $result = format_currency(0);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('Rp', $result);
    }

    /**
     * Test format_currency dengan negative value
     */
    public function test_format_currency_with_negative(): void
    {
        $company = Company::factory()->create(['currency' => 'IDR']);
        session(['active_company_id' => $company->id]);

        $result = format_currency(-1000000);

        // Should handle negative values
        $this->assertNotEmpty($result);
        $this->assertStringContainsString('-', $result);
    }

    /**
     * Test currency cache
     */
    public function test_currency_cache(): void
    {
        $company = Company::factory()->create(['currency' => 'IDR']);
        session(['active_company_id' => $company->id]);

        // First call - should hit database
        $result1 = format_currency(1000);

        // Check cache key exists
        $cached = Cache::get('company_' . $company->id);
        $this->assertNotNull($cached);
        $this->assertEquals($company->id, $cached->id);

        // Second call - should use cache
        $result2 = format_currency(1000);

        // Results should be same
        $this->assertEquals($result1, $result2);
    }
}
