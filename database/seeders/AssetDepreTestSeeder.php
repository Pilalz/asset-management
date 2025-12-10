<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use App\Scopes\CompanyScope;

class AssetDepreTestSeeder extends Seeder
{
    public function run()
    {
        $assetName = AssetName::withoutGlobalScope(CompanyScope::class)->first();
        $location  = Location::withoutGlobalScope(CompanyScope::class)->first();
        $dept      = Department::withoutGlobalScope(CompanyScope::class)->first();
        $companyId = 1;

        // CASE 1 — Depresiasi Normal
        Asset::create([
            'asset_number' => 'TEST-DEP-01',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'Normal asset',
            'detail' => null,
            'pareto' => null,
            'unit_no' => null,
            'user' => null,
            'sn' => null,
            'sn_chassis' => null,
            'sn_engine' => null,
            'production_year' => '2023-01-01',
            'po_no' => 'PO001',
            'location_id' => $location->id,
            'department_id' => $dept->id,
            'quantity' => 1,
            'capitalized_date' => '2024-01-01',
            'start_depre_date' => '2024-01-01',
            'acquisition_value' => 120000000,
            'current_cost' => 120000000,
            'commercial_useful_life_month' => 60,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => 120000000,
            'fiscal_useful_life_month' => 60,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 120000000,
            'remaks' => null,
            'company_id' => $companyId,
        ]);

        // CASE 2 — Baru mulai depresiasi bulan ini
        Asset::create([
            'asset_number' => 'TEST-DEP-02',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'Start this month',
            'location_id' => $location->id,
            'department_id' => $dept->id,
            'quantity' => 1,
            'capitalized_date' => now()->subDays(10),
            'start_depre_date' => now()->startOfMonth(),
            'acquisition_value' => 60000000,
            'current_cost' => 60000000,
            'commercial_useful_life_month' => 36,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => 60000000,
            'fiscal_useful_life_month' => 36,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 60000000,
            'company_id' => $companyId,
        ]);

        // CASE 3 — Aset lama, umur jalan
        Asset::create([
            'asset_number' => 'TEST-DEP-03',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'Old asset',
            'location_id' => $location->id,
            'department_id' => $dept->id,
            'quantity' => 1,
            'capitalized_date' => '2022-01-01',
            'start_depre_date' => '2022-01-01',
            'acquisition_value' => 100000000,
            'current_cost' => 100000000,
            'commercial_useful_life_month' => 60,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => 100000000,
            'fiscal_useful_life_month' => 60,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 100000000,
            'company_id' => $companyId,
        ]);

        // CASE 4 — Fully depreciated
        Asset::create([
            'asset_number' => 'TEST-DEP-04',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'Fully depreciated',
            'location_id' => $location->id,
            'department_id' => $dept->id,
            'quantity' => 1,
            'capitalized_date' => '2018-01-01',
            'start_depre_date' => '2018-01-01',
            'acquisition_value' => 30000000,
            'current_cost' => 30000000,
            'commercial_useful_life_month' => 36,
            'commercial_accum_depre' => 30000000,
            'commercial_nbv' => 0,
            'fiscal_useful_life_month' => 36,
            'fiscal_accum_depre' => 30000000,
            'fiscal_nbv' => 0,
            'company_id' => $companyId,
        ]);

        // CASE 5 — Tanpa start_depre_date
        Asset::create([
            'asset_number' => 'TEST-DEP-05',
            'asset_name_id' => $assetName->id,
            'asset_type' => 'FA',
            'status' => 'Active',
            'description' => 'No start date',
            'location_id' => $location->id,
            'department_id' => $dept->id,
            'quantity' => 1,
            'capitalized_date' => '2023-06-01',
            'start_depre_date' => null,
            'acquisition_value' => 50000000,
            'current_cost' => 50000000,
            'commercial_useful_life_month' => 60,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => 50000000,
            'fiscal_useful_life_month' => 60,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => 50000000,
            'company_id' => $companyId,
        ]);
    }
}