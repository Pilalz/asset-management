<?php

namespace App\Imports;

use App\Models\Asset;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Asset([
            'asset_number'      => $row['asset_number'],
            'asset_name_id'     => $row['asset_name_id'],
            'status'            => $row['status'],
            'description'       => $row['description'],
            'detail'            => $row['detail'] ?? null,
            'pareto'            => $row['pareto'] ?? null,
            'unit_no'           => $row['unit_no'] ?? null,
            'sn_chassis'        => $row['sn_chassis'] ?? null,
            'sn_engine'         => $row['sn_engine'] ?? null,
            'po_no'             => $row['po_no'] ?? null,
            'location_id'       => $row['location_id'],
            'department_id'     => $row['department_id'],
            'quantity'          => $row['quantity'],
            'capitalized_date'  => !empty($row['capitalized_date']) ? Date::excelToDateTimeObject($row['capitalized_date']) : null,
            'start_depre_date'  => !empty($row['start_depre_date']) ? Date::excelToDateTimeObject($row['start_depre_date']) : null,
            'acquisition_value' => $row['acquisition_value'],
            'current_cost'      => $row['current_cost'],
            'useful_life_month' => $row['useful_life_month'],
            'accum_depre'       => $row['accum_depre'],
            'net_book_value'    => $row['net_book_value'],
            'company_id'        => session('active_company_id'),
        ]);
    }
}
