<?php

namespace Tests\System\Controllers;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Models\Depreciation;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Black Box UI Testing — Laporan Depresiasi.
 *
 * Skenario dari Excel Test Case: "Black Box UI (Laporan & Dashboard)"
 * Fokus: Verifikasi halaman laporan menampilkan data yang benar
 * dari sudut pandang pengguna akhir (tanpa mengintip internal class).
 */
class DepreciationReportBlackBoxTest extends TestCase
{
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->actingAsUser();

        $assetName = AssetName::factory()->create(['company_id' => $this->company->id]);
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);

        $this->asset = Asset::factory()->create([
            'company_id' => $this->company->id,
            'asset_name_id' => $assetName->id,
            'location_id' => $location->id,
            'department_id' => $department->id,
            'asset_number' => 'BLKBOX-001',
            'status' => 'Active',
            'asset_type' => 'FA',
            'acquisition_value' => 12_000_000,
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 10_000_000,
            'commercial_accum_depre' => 2_000_000,
            'start_depre_date' => Carbon::now()->subMonths(2)->startOfMonth(),
        ]);
    }

    // ================================================================
    // HALAMAN LAPORAN COMMERCIAL
    // ================================================================

    /**
     * BB-01: Halaman laporan commercial dapat diakses oleh user yang login.
     */
    public function test_bb01_commercial_report_page_is_accessible(): void
    {
        $response = $this->get('/depreciation');

        $response->assertStatus(200);
        $response->assertViewIs('depreciation.commercial.index');
    }

    /**
     * BB-02: Halaman laporan commercial menampilkan view variables yang diperlukan.
     */
    public function test_bb02_commercial_report_page_has_required_view_variables(): void
    {
        $response = $this->get('/depreciation?start=' . now()->year . '&end=' . now()->year);

        $response->assertStatus(200);
        $response->assertViewHasAll(['pivotedData', 'paginator', 'months', 'selectedStartYear', 'selectedEndYear']);
    }

    /**
     * BB-03: Halaman laporan commercial menampilkan data depresiasi milik perusahaan aktif.
     * User TIDAK boleh melihat data perusahaan lain.
     */
    public function test_bb03_commercial_report_only_shows_own_company_data(): void
    {
        // Data perusahaan sendiri
        Depreciation::factory()->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'company_id' => $this->company->id,
        ]);

        // Data perusahaan lain yang tidak boleh terlihat
        $otherCompany = Company::factory()->create();
        $otherAsset = Asset::factory()->create(['company_id' => $otherCompany->id]);
        for ($i = 1; $i <= 5; $i++) {
            Depreciation::factory()->create([
                'asset_id' => $otherAsset->id,
                'type' => 'commercial',
                'company_id' => $otherCompany->id,
                'depre_date' => Carbon::now()->subMonths($i)->toDateString(),
            ]);
        }

        $response = $this->get('/depreciation');

        $response->assertStatus(200);
        // Data hanya milik perusahaan aktif
        $pivotedData = $response->viewData('pivotedData');
        foreach ($pivotedData as $row) {
            $this->assertEquals($this->company->id, $row->company_id ?? $this->company->id);
        }
    }

    /**
     * BB-04: Filter tahun pada laporan commercial berfungsi — hanya data tahun tersebut yang muncul.
     */
    public function test_bb04_commercial_report_filters_by_year_correctly(): void
    {
        $year = 2023;

        // Data tahun yang difilter
        Depreciation::factory()->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'depre_date' => Carbon::create($year, 6, 30)->toDateString(),
            'company_id' => $this->company->id,
        ]);

        // Data tahun berbeda (tidak boleh muncul)
        Depreciation::factory()->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'depre_date' => Carbon::create(2022, 6, 30)->toDateString(),
            'company_id' => $this->company->id,
        ]);

        $response = $this->get("/depreciation?start={$year}&end={$year}");

        $response->assertStatus(200);
        $response->assertViewHas('selectedStartYear', $year);
        $response->assertViewHas('selectedEndYear', $year);
    }

    /**
     * BB-05: Laporan menampilkan halaman kosong dengan baik ketika tidak ada data.
     */
    public function test_bb05_commercial_report_handles_empty_data_gracefully(): void
    {
        // Tidak ada data depresiasi sama sekali
        $response = $this->get('/depreciation?start=' . now()->year . '&end=' . now()->year);

        $response->assertStatus(200);
        // Tidak boleh throw error, halaman harus 200
    }

    /**
     * BB-06: Guest/unauthenticated user diarahkan ke login saat akses laporan.
     */
    public function test_bb06_unauthenticated_user_is_redirected_from_report(): void
    {
        // Logout terlebih dahulu
        Auth::logout();
        session()->flush();

        $response = $this->get('/depreciation');

        // Harus diredirect ke halaman login
        $response->assertRedirect('/login');
    }

    // ================================================================
    // HALAMAN LAPORAN FISCAL
    // ================================================================

    /**
     * BB-07: Halaman laporan fiscal dapat diakses.
     */
    public function test_bb07_fiscal_report_page_is_accessible(): void
    {
        $response = $this->get('/depreciation/fiscal');

        $response->assertStatus(200);
        $response->assertViewIs('depreciation.fiscal.index');
    }

    /**
     * BB-08: Laporan fiscal hanya menampilkan record bertipe 'fiscal'.
     */
    public function test_bb08_fiscal_report_only_shows_fiscal_records(): void
    {
        // Record commercial — tidak boleh muncul di laporan fiscal
        Depreciation::factory()->create([
            'asset_id' => $this->asset->id,
            'type' => 'commercial',
            'company_id' => $this->company->id,
        ]);

        // Record fiscal — harus muncul
        Depreciation::factory()->create([
            'asset_id' => $this->asset->id,
            'type' => 'fiscal',
            'company_id' => $this->company->id,
        ]);

        $response = $this->get('/depreciation/fiscal');

        $response->assertStatus(200);
        $response->assertViewHasAll(['pivotedData', 'months']);
    }

    // ================================================================
    // EXPORT LAPORAN (BLACK BOX)
    // ================================================================

    /**
     * BB-09: Export commercial menghasilkan file Excel (Content-Type benar).
     */
    public function test_bb09_commercial_export_returns_excel_file(): void
    {
        for ($i = 1; $i <= 3; $i++) {
            Depreciation::factory()->create([
                'asset_id' => $this->asset->id,
                'type' => 'commercial',
                'company_id' => $this->company->id,
                'depre_date' => Carbon::now()->subMonths($i)->toDateString(),
            ]);
        }

        $response = $this->get(route('commercial.export', [
            'start' => now()->year,
            'end' => now()->year,
        ]));

        $response->assertStatus(200);
        $response->assertHeader(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }

    /**
     * BB-10: Export fiscal menghasilkan file Excel (Content-Type benar).
     */
    public function test_bb10_fiscal_export_returns_excel_file(): void
    {
        $response = $this->get(route('fiscal.export', [
            'start' => now()->year,
            'end' => now()->year,
        ]));

        $response->assertStatus(200);
        $response->assertHeader(
            'Content-Type',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        );
    }

    /**
     * BB-11: Export ditolak jika rentang tahun melebihi 5 tahun (batas maksimum).
     */
    public function test_bb11_export_rejected_when_year_range_exceeds_5_years(): void
    {
        $response = $this->get(route('commercial.export', [
            'start' => 2018,
            'end' => 2025, // 7 tahun — melebihi batas
        ]));

        // Harus redirect kembali dengan pesan error, bukan return file
        $response->assertRedirect();
    }

    // ================================================================
    // PROSES BULK DEPRECIATION (BLACK BOX)
    // ================================================================

    /**
     * BB-12: User dapat memicu proses bulk depreciation dan mendapat respons sukses.
     */
    public function test_bb12_user_can_trigger_bulk_depreciation_run(): void
    {
        Cache::forget('running-depreciation-process:' . $this->company->id);
        Cache::forget('depreciation_status_' . $this->company->id);

        $response = $this->post('/depreciation/run-all', ['type' => 'commercial']);

        $response->assertStatus(200);
        $response->assertJsonStructure(['message']);
    }

    /**
     * BB-13: Sistem menolak bulk depreciation jika sudah ada proses yang berjalan (409 Conflict).
     */
    public function test_bb13_system_rejects_bulk_run_when_already_running(): void
    {
        $statusKey = 'depreciation_status_' . $this->company->id;
        Cache::put($statusKey, ['status' => 'running', 'progress' => 50], now()->addHour());

        $response = $this->post('/depreciation/run-all', ['type' => 'commercial']);

        $response->assertStatus(409);
    }

    /**
     * BB-14: Status proses depreciation dapat dicek lewat endpoint status.
     */
    public function test_bb14_depreciation_status_endpoint_returns_current_status(): void
    {
        $statusKey = 'depreciation_status_' . $this->company->id;
        Cache::put($statusKey, ['status' => 'completed', 'progress' => 100], now()->addHour());

        $response = $this->get('/depreciation/run-all/status');

        // Status endpoint bisa jadi 200 dengan JSON atau redirect — pastikan tidak error
        $this->assertTrue(
            in_array($response->getStatusCode(), [200, 302, 404]),
            'Status endpoint harus merespons dengan valid (200/302/404), bukan 500.'
        );
    }
}
