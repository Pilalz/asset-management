<?php

namespace Tests\System\Controllers;

use Tests\TestCase;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use Carbon\Carbon;

class DepreciationFlowTest extends TestCase
{
    /**
     * Skenario: Admin input aset -> Jalankan penyusutan -> Cek Nilai Buku -> Export Laporan.
     */
    public function test_full_admin_depreciation_workflow(): void
    {
        $this->actingAsUser();
        
        $location = Location::factory()->create(['company_id' => $this->company->id]);
        $department = Department::factory()->create(['company_id' => $this->company->id]);
        $name = AssetName::factory()->create([
            'commercial' => '12',
            'company_id' => $this->company->id,
        ]);

        // 1. Input Aset Baru via Request POST
        // Nilai Akuisisi = 12.000.000, Umur = 12 Bulan, Penyusutan = 1.000.000/bulan
        $assetData = [
            'asset_number' => 'E2E-001',
            'asset_code' => 'AC-' . \Illuminate\Support\Str::uuid(),
            'asset_name_id' => $name->id,
            'status' => 'Active',
            'asset_type' => 'FA',
            'acquisition_value' => 12000000,
            'commercial_useful_life_month' => 12,
            'commercial_nbv' => 12000000,
            'commercial_accum_depre' => 0,
            'start_depre_date' => Carbon::now()->subMonths(2)->startOfMonth()->format('Y-m-d'),
            'company_id' => $this->company->id,

            'po_no' => 'PO8339256',
            'description' => 'Test Asset',
            'location_id' => $location->id,
            'department_id' => $department->id,
            'quantity' => 1,
            'capitalized_date' => '2024-01-01',
            'current_cost' => 12000000,
            'fiscal_useful_life_month' => 12,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 12000000,
        ];
        
        $response = $this->post('/asset', $assetData);

        $response->assertSessionHasNoErrors();

        // 2. Jalankan Proses Penyusutan Masal
        $this->post('/depreciation/run-all', ['type' => 'commercial'])->assertOk();

        // 3. Verifikasi Logika Matematika di Database
        // Rumus: $NBV = Acquisition - (Monthly \times Months\ Passed)$
        // $12jt - (1jt \times 2\ bulan) = 10jt$
        $asset = Asset::where('asset_number', 'E2E-001')
            ->where('company_id', $this->company->id)
            ->first();
        
        $this->assertEquals(10000000, (int)$asset->commercial_nbv);
        $this->assertEquals(2000000, (int)$asset->commercial_accum_depre);
        
        // Pastikan ada 2 record history penyusutan
        $this->assertDatabaseCount('depreciations', 2);

        // 4. Verifikasi Laporan Excel bisa dihasilkan
        $response = $this->get(route('commercial.export', ['year' => now()->year]));
        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }
}