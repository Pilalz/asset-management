<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetName;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LVAImport implements ToModel, WithStartRow, WithValidation
{
    public function startRow(): int
    {
        return 3;
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

        $productionYear = null;
        if (!empty($row[8]) && is_numeric($row[8])) {
            $productionYear = $row[8] . '-01-01';
        }
        
        return new Asset([
            'asset_number'                  => $row[0],
            'asset_name_id'                 => $row[1],
            'description'                   => $row[2],
            'detail'                        => $row[3] ?? null,
            'pareto'                        => $row[4] ?? null,
            'unit_no'                       => $row[5] ?? null,
            'sn_chassis'                    => $row[6] ?? null,
            'sn_engine'                     => $row[7] ?? null,
            'production_year'               => $productionYear,
            'po_no'                         => $row[9] ?? null,
            'location_id'                   => $row[10],
            'department_id'                 => $row[11],
            'quantity'                      => $row[12],
            'capitalized_date'              => !empty($row[13]) ? Date::excelToDateTimeObject($row[13]) : null,
            'acquisition_value'             => $row[14],
            'current_cost'                  => $row[14],
            'commercial_useful_life_month'  => $commercialUsefulLife,
            'commercial_accum_depre'        => 0,
            'commercial_nbv'                => $row[14],
            'fiscal_useful_life_month'      => $fiscalUsefulLife,
            'fiscal_accum_depre'            => 0,
            'fiscal_nbv'                    => $row[14],
            'company_id'                    => session('active_company_id'),
            'status'                        => 'Active',
            'asset_type'                    => 'LVA',
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'unique:assets,asset_number',
            '1' => 'required|exists:asset_names,id',
            '2' => 'required|string|max:255',
            '3' => 'nullable|string|max:255',
            '4' => 'nullable|string|max:255',
            '5' => 'nullable|string|max:255',
            '6' => 'nullable|string|max:255',
            '7' => 'nullable|string|max:255',
            '8' => 'nullable',
            '9' => 'nullable|string|max:255',
            '10' => 'required|exists:locations,id',
            '11' => 'required|exists:departments,id',
            '11' => 'required|exists:departments,id',
            '12' => 'required',
            '13' => 'required',
            '14' => 'required',
        ];
    }
}
