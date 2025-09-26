<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class AssetsImport implements ToModel, WithStartRow, WithValidation
{
    private $assetNames;
    private $locations;
    private $departments;

    public function __construct()
    {
        $this->assetNames  = AssetName::all()->keyBy('name');
        $this->locations   = Location::all()->keyBy('name');
        $this->departments = Department::all()->keyBy('name');
    }

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
        $assetName   = $this->assetNames->get($row[1]);
        $location    = $this->locations->get($row[10]);
        $department  = $this->departments->get($row[11]);

        $commercialUsefulLife = $assetName ? $assetName->commercial * 12 : 0;
        $fiscalUsefulLife     = $assetName ? $assetName->fiscal * 12 : 0;

        $productionYear = null;
        if (!empty($row[8]) && is_numeric($row[8])) {
            $productionYear = $row[8] . '-01-01';
        }
        
        return new Asset([
            'asset_number'                  => $row[0],
            'asset_name_id'                 => $assetName ? $assetName->id : null,
            'description'                   => $row[2],
            'detail'                        => $row[3] ?? null,
            'pareto'                        => $row[4] ?? null,
            'unit_no'                       => $row[5] ?? null,
            'sn_chassis'                    => $row[6] ?? null,
            'sn_engine'                     => $row[7] ?? null,
            'production_year'               => $productionYear,
            'po_no'                         => $row[9] ?? null,
            'location_id'                   => $location ? $location->id : null,
            'department_id'                 => $department ? $department->id : null,
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

    public function rules(): array
    {
        return [
            '0' => 'unique:assets,asset_number',
            '1' => 'required|exists:asset_names,name',
            '2' => 'required|string|max:255',
            '3' => 'nullable|string|max:255',
            '4' => 'nullable|string|max:255',
            '5' => 'nullable|string|max:255',
            '6' => 'nullable|string|max:255',
            '7' => 'nullable|string|max:255',
            '8' => 'nullable',
            '9' => 'nullable|string|max:255',
            '10' => 'required|exists:locations,name',
            '11' => 'required|exists:departments,name',
            '12' => 'required',
            '13' => 'required',
            '14' => 'required',
            '15' => 'required',
        ];
    }
}
