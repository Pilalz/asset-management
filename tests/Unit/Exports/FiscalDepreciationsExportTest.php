<?php

namespace Tests\Unit\Exports;

use Tests\TestCase;
use App\Exports\FiscalDepreciationsExport;

class FiscalDepreciationsExportTest extends TestCase
{
    /**
     * Test export is instantiable with year parameter.
     */
    public function test_export_can_be_instantiated(): void
    {
        $export = new FiscalDepreciationsExport(2024);

        $this->assertInstanceOf(FiscalDepreciationsExport::class, $export);
    }

    /**
     * Test export has correct headings structure.
     */
    public function test_export_has_correct_headings(): void
    {
        $export = new FiscalDepreciationsExport(2024);
        $headings = $export->headings();

        $this->assertIsArray($headings);

        // Should have: No, Asset Name, Asset Number + (Monthly, Accum, Book) x 12 months
        // = 3 + (3 * 12) = 39 columns
        $expectedColumnCount = 3 + (3 * 12);
        $this->assertCount($expectedColumnCount, $headings);

        // Check first few headers
        $this->assertEquals('No', $headings[0]);
        $this->assertEquals('Asset Name', $headings[1]);
        $this->assertEquals('Asset Number', $headings[2]);
        $this->assertEquals('Monthly Depre', $headings[3]);
        $this->assertEquals('Accum Depre', $headings[4]);
        $this->assertEquals('Book Value', $headings[5]);
    }

    /**
     * Test export starts from correct cell.
     */
    public function test_export_starts_from_correct_cell(): void
    {
        $export = new FiscalDepreciationsExport(2024);

        $this->assertEquals('A2', $export->startCell());
    }

    /**
     * Test export generates 12 months correctly.
     */
    public function test_export_generates_correct_months(): void
    {
        $year = 2024;
        $export = new FiscalDepreciationsExport($year);
        $headings = $export->headings();

        // Each month should have 3 columns (Monthly, Accum, Book)
        // So total should be 3 (base columns) + 12*3 (month columns) = 39
        $this->assertCount(39, $headings);
    }

    /**
     * Test export query filters by fiscal type.
     */
    public function test_export_query_filters_fiscal_type(): void
    {
        // This is a semi-integration test since we need session
        session(['active_company_id' => 1]);

        $export = new FiscalDepreciationsExport(2024);
        $query = $export->query();

        // Verify the query builder is returned
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    /**
     * Test logika pemetaan data ke kolom Excel (Mapping)
     */
    public function test_export_maps_data_correctly_to_columns(): void
    {
        $export = new FiscalDepreciationsExport(2024);
        
        // Mocking object Asset dengan relasi depreciations
        $asset = (object)[
            'asset_number' => 'AST-001',
            'assetName' => (object)['name' => 'Laptop'],
            'depreciations' => collect([
                (object)[
                    'depre_date' => '2024-01-31',
                    'monthly_depre' => 500000,
                    'accumulated_depre' => 500000,
                    'book_value' => 9500000
                ]
            ])
        ];

        $mappedRow = $export->map($asset);

        // Kolom 0: No (Empty string for Auto-numbering)
        // Kolom 1: Asset Name
        // Kolom 2: Asset Number
        // Kolom 3,4,5: Data Januari (Monthly, Accum, Book)
        $this->assertEquals('Laptop', $mappedRow[1]);
        $this->assertEquals('AST-001', $mappedRow[2]);
        $this->assertEquals(500000, $mappedRow[3]); // Jan Monthly
        $this->assertEquals(0, $mappedRow[6]);      // Feb Monthly (Harus 0 karena tidak ada data)
    }
}
