<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetName;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToModel, WithStartRow
{
    public function startRow(): int
    {
        return 2;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $assetName = AssetName::find($row[1]);
        $commercialUsefulLife = $assetName ? $assetName->commercial * 12 : 0;
        $fiscalUsefulLife = $assetName ? $assetName->fiscal * 12 : 0;
        
        return new Asset([
            'asset_number'                  => $row[0],
            'asset_name_id'                 => $row[1],
            'description'                   => $row[2],
            'detail'                        => $row[3] ?? null,
            'pareto'                        => $row[4] ?? null,
            'unit_no'                       => $row[5] ?? null,
            'sn_chassis'                    => $row[6] ?? null,
            'sn_engine'                     => $row[7] ?? null,
            'production_year'               => $row[8] ?? null,
            'po_no'                         => $row[9] ?? null,
            'location_id'                   => $row[10],
            'department_id'                 => $row[11],
            'quantity'                      => $row[12],
            'capitalized_date'              => !empty($row[13]) ? Date::excelToDateTimeObject($row[13]) : null,
            'start_depre_date'              => !empty($row[14]) ? Date::excelToDateTimeObject($row[14]) : null,
            'acquisition_value'             => $row[15],
            'current_cost'                  => $row[15],
            'commercial_useful_life_month'  => $commercialUsefulLife,
            'commercial_accum_depre'        => 0,
            'commercial_nbv'                => $row[15],
            'fiscal_useful_life_month'      => $fiscalUsefulLife,
            'fiscal_accum_depre'            => 0,
            'fiscal_nbv'                    => $row[15],
            'company_id'                    => session('active_company_id'),
            'status'                        => 'Active',
            'asset_type'                    => 'FA',
        ]);
    }
}
