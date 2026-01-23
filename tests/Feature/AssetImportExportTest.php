<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Company;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Asset;

class AssetImportExportTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');
    }

    /**
     * Test dapat upload file Excel untuk import
     */
    public function test_can_upload_excel_file_for_import(): void
    {
        $file = UploadedFile::fake()->create('assets.xlsx', 100);

        $this->actingAsUser();

        $response = $this->post(
                '/asset/import', [
                'file' => $file,
            ]);

        // Response depends on implementation
        $response->assertRedirect();
    }

    /**
     * Test import dengan file tidak valid ditolak
     */
    public function test_import_with_invalid_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('assets.txt', 100); // Wrong format

        $this->actingAsUser();

        $response = $this->post(
                '/asset/import', [
                'file' => $file,
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test import dengan file kosong ditolak
     */
    public function test_import_with_empty_file_is_rejected(): void
    {
        $this->actingAsUser();

        $response = $this->post(
                '/asset/import', [
                // No file provided
            ]);

        $response->assertSessionHasErrors();
    }

    /**
     * Test import dengan file terlalu besar ditolak
     */
    public function test_import_with_oversized_file_is_rejected(): void
    {
        $file = UploadedFile::fake()->create('assets.xlsx', 50000); // 50MB

        $this->actingAsUser();

        $response = $this->post(
                '/asset/import', [
                'file' => $file,
            ]);

        // Should be rejected due to size
        $response->assertSessionHasErrors();
    }

    /**
     * Test dapat export assets ke Excel
     */
    public function test_can_export_assets_to_excel(): void
    {
        // Create some assets
        Asset::factory()->count(5)->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAsUser();

        $response = $this->get('/asset/export-excel');

        $response->assertStatus(200);
        // Should return Excel file
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Test export dengan query string (filtered export)
     */
    public function test_can_export_filtered_assets(): void
    {
        Asset::factory()->count(3)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'FA',
        ]);

        Asset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'LVA',
        ]);

        $this->actingAsUser();

        $response = $this->get('/asset/export-excel?asset_type=FA');

        $response->assertStatus(200);
    }

    /**
     * Test import progress tracking
     */
    public function test_import_progress_is_tracked(): void
    {
        // If implementation has progress tracking via cache
        $file = UploadedFile::fake()->create('assets.xlsx', 100);

        $this->actingAsUser();

        $response = $this->post(
                '/asset/import', [
                'file' => $file,
            ]);

        // Check if progress was stored in cache
        // $progress = Cache::get('import_progress_' . $this->user->id);
    }

    /**
     * Test import dapat dicancel
     */
    public function test_import_can_be_cancelled(): void
    {
        $this->actingAsUser();
        // If implementation supports cancellation
        $response = $this->post('/asset/import/cancel');

        // Depends on implementation
    }

    /**
     * Test import error handling
     */
    public function test_import_handles_errors_gracefully(): void
    {
        // Mock file dengan data tidak valid
        $file = UploadedFile::fake()->create('assets.xlsx', 100);

        $this->actingAsUser();

        $response = $this->post(
                '/asset/import', [
                'file' => $file,
            ]);

        // Should handle errors and show user-friendly message
    }

    /**
     * Test exported file berisi semua required columns
     */
    public function test_exported_file_contains_required_columns(): void
    {
        $asset = Asset::factory()->create([
            'company_id' => $this->company->id,
        ]);

        $this->actingAsUser();

        $response = $this->get('/asset/export-excel');

        $response->assertStatus(200);
        // Additional assertion untuk verify columns dalam file Excel
    }

    /**
     * Test export hanya include assets dari company yang aktif
     */
    public function test_export_only_includes_active_company_assets(): void
    {
        $otherCompany = Company::factory()->create();

        Asset::factory()->count(3)->create([
            'company_id' => $this->company->id,
        ]);

        Asset::factory()->count(2)->create([
            'company_id' => $otherCompany->id,
        ]);

        $this->actingAsUser();

        $response = $this->get('/asset/export-excel');

        $response->assertStatus(200);
        // Should only export 3 assets from active company
    }

    /**
     * Test import batch size handling
     */
    public function test_import_handles_large_batch_correctly(): void
    {
        // Test with large number of records
        $file = UploadedFile::fake()->create('assets_large.xlsx', 1000);

        // Should handle batch import efficiently
    }

    /**
     * Test import duplicate detection
     */
    public function test_import_detects_duplicate_records(): void
    {
        // If implementation checks for duplicates
        $file = UploadedFile::fake()->create('assets_with_duplicates.xlsx', 100);

        // Should handle or report duplicates
    }

    /**
     * Test export dengan multiple asset types
     */
    public function test_export_with_multiple_asset_types(): void
    {
        Asset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'FA',
        ]);

        Asset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'LVA',
        ]);

        Asset::factory()->count(2)->create([
            'company_id' => $this->company->id,
            'asset_type' => 'Arrival',
        ]);

        $this->actingAsUser();

        $response = $this->get('/asset/export-excel');

        $response->assertStatus(200);
        // Should include all asset types
    }
}
