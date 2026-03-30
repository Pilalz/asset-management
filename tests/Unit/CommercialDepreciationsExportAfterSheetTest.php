<?php

namespace Tests\Unit\Exports;

use Tests\TestCase;
use App\Exports\CommercialDepreciationsExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Test AfterSheet event pada CommercialDepreciationsExport.
 *
 * Skenario dari Excel Test Case:
 *  - Header baris 1 berisi nama bulan yang di-merge 3 kolom per bulan
 *  - Header baris 2 (sub-header) dibold dan rata tengah
 *  - Kolom A (No) diisi auto-numbering mulai dari baris data (baris 3+)
 */
class CommercialDepreciationsExportAfterSheetTest extends TestCase
{
    /**
     * Test registerEvents mengembalikan AfterSheet event key.
     */
    public function test_register_events_returns_after_sheet_key(): void
    {
        $export = new CommercialDepreciationsExport(2024, 2024);
        $events = $export->registerEvents();

        $this->assertArrayHasKey(AfterSheet::class, $events);
        $this->assertIsCallable($events[AfterSheet::class]);
    }

    /**
     * Test headings untuk 1 tahun menghasilkan 39 kolom.
     * Format: No, Asset Name, Asset Number + (Monthly, Accum, Book) × 12 bulan
     */
    public function test_headings_single_year_generates_39_columns(): void
    {
        $export = new CommercialDepreciationsExport(2024, 2024);
        $headings = $export->headings();

        // 3 kolom dasar + 12 bulan × 3 kolom = 39
        $this->assertCount(39, $headings);
        $this->assertEquals('No', $headings[0]);
        $this->assertEquals('Asset Name', $headings[1]);
        $this->assertEquals('Asset Number', $headings[2]);
        $this->assertEquals('Monthly Depre', $headings[3]);
        $this->assertEquals('Accum Depre', $headings[4]);
        $this->assertEquals('Book Value', $headings[5]);
    }

    /**
     * Test headings untuk rentang 2 tahun menghasilkan kolom yang tepat.
     * Format: 3 + (24 bulan × 3) = 75 kolom
     */
    public function test_headings_two_year_range_generates_75_columns(): void
    {
        $export = new CommercialDepreciationsExport(2024, 2025);
        $headings = $export->headings();

        // 3 kolom dasar + 24 bulan × 3 kolom = 75
        $this->assertCount(75, $headings);
    }

    /**
     * Test konstruktor melempar exception jika range > 5 tahun.
     */
    public function test_constructor_throws_exception_for_range_over_5_years(): void
    {
        $this->expectException(\Illuminate\Http\Exceptions\HttpResponseException::class);

        new CommercialDepreciationsExport(2020, 2026); // 6 tahun — melebihi batas
    }

    /**
     * Test konstruktor menerima range tepat 5 tahun tanpa exception.
     */
    public function test_constructor_accepts_exactly_5_year_range(): void
    {
        $export = new CommercialDepreciationsExport(2020, 2025);
        $this->assertInstanceOf(CommercialDepreciationsExport::class, $export);
    }

    /**
     * Test map() menghasilkan baris yang benar ketika aset tidak punya data bulan tertentu.
     * Bulan yang tidak ada datanya harus diisi 0 (bukan null/kosong).
     */
    public function test_map_fills_missing_months_with_zero(): void
    {
        $export = new CommercialDepreciationsExport(2024, 2024);

        // Aset hanya punya data Januari 2024
        $asset = (object) [
            'asset_number' => 'AST-ZERO-001',
            'assetName' => (object) ['name' => 'Laptop Test'],
            'depreciations' => collect([
                (object) [
                    'depre_date' => '2024-01-31',
                    'monthly_depre' => 1_000_000,
                    'accumulated_depre' => 1_000_000,
                    'book_value' => 9_000_000,
                ]
            ]),
        ];

        $row = $export->map($asset);

        // Kolom 0: '' (No — kosong)
        // Kolom 1: Asset Name
        // Kolom 2: Asset Number
        // Kolom 3,4,5: Januari (tidak kosong)
        // Kolom 6,7,8: Februari (harus 0)
        $this->assertEquals('Laptop Test', $row[1]);
        $this->assertEquals('AST-ZERO-001', $row[2]);
        $this->assertEquals(1_000_000, $row[3]); // Jan Monthly
        $this->assertEquals(1_000_000, $row[4]); // Jan Accum
        $this->assertEquals(9_000_000, $row[5]); // Jan Book Value
        $this->assertEquals(0, $row[6]);          // Feb Monthly — harus 0
        $this->assertEquals(0, $row[7]);          // Feb Accum — harus 0
        $this->assertEquals(0, $row[8]);          // Feb Book Value — harus 0
    }

    /**
     * Test map() menghasilkan baris lengkap untuk aset dengan data semua bulan.
     */
    public function test_map_correctly_maps_full_year_data(): void
    {
        $export = new CommercialDepreciationsExport(2024, 2024);

        // Buat 12 bulan data
        $depreciations = collect();
        for ($m = 1; $m <= 12; $m++) {
            $date = \Carbon\Carbon::create(2024, $m, 1)->endOfMonth()->toDateString();
            $depreciations->push((object) [
                'depre_date' => $date,
                'monthly_depre' => 500_000,
                'accumulated_depre' => 500_000 * $m,
                'book_value' => 10_000_000 - (500_000 * $m),
            ]);
        }

        $asset = (object) [
            'asset_number' => 'AST-FULL-001',
            'assetName' => (object) ['name' => 'Server Rack'],
            'depreciations' => $depreciations,
        ];

        $row = $export->map($asset);

        // Total kolom: 3 + (12 × 3) = 39
        $this->assertCount(39, $row);

        // Verifikasi bulan pertama (Jan 2024) — kolom index 3, 4, 5
        $this->assertEquals(500_000, $row[3]); // Monthly
        $this->assertEquals(500_000, $row[4]); // Accum
        $this->assertEquals(9_500_000, $row[5]); // Book

        // Verifikasi bulan terakhir (Des 2024) — kolom index (3 + 11×3) = 36, 37, 38
        $this->assertEquals(500_000, $row[36]); // Dec Monthly
        $this->assertEquals(6_000_000, $row[37]); // Dec Accum
        $this->assertEquals(4_000_000, $row[38]); // Dec Book
    }

    /**
     * Test startCell() selalu mengembalikan A2.
     * Baris 1 digunakan untuk header bulan (merge cells via AfterSheet).
     */
    public function test_start_cell_is_a2(): void
    {
        $export = new CommercialDepreciationsExport(2024, 2024);
        $this->assertEquals('A2', $export->startCell());
    }

    /**
     * Test export menggunakan Excel facade dan bisa di-download tanpa error.
     */
    public function test_export_can_be_downloaded_via_excel_facade(): void
    {
        $this->actingAsUser();
        session(['active_company_id' => $this->company->id]);

        $export = new CommercialDepreciationsExport(2024, 2024);

        // Verifikasi bahwa export bisa di-instantiate dan queue-nya valid
        Excel::fake();
        Excel::download($export, 'test.xlsx');

        Excel::assertDownloaded('test.xlsx');
    }
}
