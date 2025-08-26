<?php

namespace App\Imports;

use App\Models\Asset;
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
        return new Asset([
            'asset_number'      => $row[0],
            'asset_name_id'     => $row[1],
            'status'            => $row[2],
            'description'       => $row[3],
            'detail'            => $row[4] ?? null,
            'pareto'            => $row[5] ?? null,
            'unit_no'           => $row[6] ?? null,
            'sn_chassis'        => $row[7] ?? null,
            'sn_engine'         => $row[8] ?? null,
            'po_no'             => $row[9] ?? null,
            'location_id'       => $row[10],
            'department_id'     => $row[11],
            'quantity'          => $row[12],
            'capitalized_date'  => !empty($row[13]) ? Date::excelToDateTimeObject($row[13]) : null,
            'start_depre_date'  => !empty($row[14]) ? Date::excelToDateTimeObject($row[14]) : null,
            'acquisition_value' => $row[15],
            'current_cost'      => $row[16],
            'useful_life_month' => $row[17],
            'accum_depre'       => $row[18],
            'net_book_value'    => $row[19],
            'company_id'        => session('active_company_id'),
        ]);
    }
}
