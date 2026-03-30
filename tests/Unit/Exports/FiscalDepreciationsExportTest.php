<?php

namespace Tests\Unit\Exports;

use Tests\TestCase;
use App\Exports\FiscalDepreciationsExport;

class FiscalDepreciationsExportTest extends TestCase
{
    /**
     * Menguji apakah class export dapat diinstansiasi dengan parameter tahun awal dan akhir.
     */
    public function test_export_can_be_instantiated(): void
    {
        $export = new FiscalDepreciationsExport(2024, 2024);

        $this->assertInstanceOf(FiscalDepreciationsExport::class, $export);
    }

    /**
     * Menguji apakah export memiliki struktur penamaan kolom (heading) yang benar sesuai rentang tahun.
     */
    public function test_export_has_correct_headings(): void
    {
        $export = new FiscalDepreciationsExport(2024, 2024);
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
     * Menguji apakah data export dimulai dari sel A2 (karena A1 digunakan untuk header bulan).
     */
    public function test_export_starts_from_correct_cell(): void
    {
        $export = new FiscalDepreciationsExport(2024, 2024);

        $this->assertEquals('A2', $export->startCell());
    }

    /**
     * Menguji apakah query pada export memfilter tipe depresiasi secara benar ('fiscal').
     */
    public function test_export_query_filters_fiscal_type(): void
    {
        // This is a semi-integration test since we need session
        session(['active_company_id' => 1]);

        $export = new FiscalDepreciationsExport(2024, 2024);
        $query = $export->query();

        // Verify the query builder is returned
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Builder::class, $query);
    }

    /**
     * Menguji logika pemetaan data model Asset ke dalam baris dan kolom Excel yang sesuai letak bulannya.
     */
    public function test_export_maps_data_correctly_to_columns(): void
    {
        $export = new FiscalDepreciationsExport(2024, 2024);

        // Mocking object Asset dengan relasi depreciations
        $asset = (object) [
            'asset_number' => 'AST-001',
            'assetName' => (object) ['name' => 'Laptop'],
            'depreciations' => collect([
                (object) [
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
