<?php

namespace App\Imports;

use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Location;
use App\Models\Department;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class LVAImport implements ToModel, WithStartRow, WithValidation
{
    private $assetNames;
    private $locations;
    private $departments;
    private $companyId;

    public function __construct()
    {
        $this->companyId = session('active_company_id');
        $this->assetNames  = AssetName::where('company_id', $this->companyId)
            ->get()
            ->keyBy('name');
        $this->locations   = Location::where('company_id', $this->companyId)
            ->get()
            ->keyBy('name');
        $this->departments = Department::where('company_id', $this->companyId)
            ->get()
            ->keyBy('name');
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
        $location    = $this->locations->get($row[7]);
        $department  = $this->departments->get($row[8]);
        
        $commercialUsefulLife = $assetName ? $assetName->commercial * 12 : 0;
        $fiscalUsefulLife     = $assetName ? $assetName->fiscal * 12 : 0;
        
        return new Asset([
            'asset_number'                  => $row[0],
            'asset_name_id'                 => $assetName ? $assetName->id : null,
            'description'                   => $row[2],
            'detail'                        => $row[3] ?? null,
            'sn'                            => $row[4] ?? null,
            'user'                          => $row[5] ?? null,
            'po_no'                         => $row[6] ?? null,
            'location_id'                   => $location ? $location->id : null,
            'department_id'                 => $department ? $department->id : null,
            'quantity'                      => $row[9],
            'capitalized_date'              => !empty($row[10]) ? Date::excelToDateTimeObject($row[10]) : null,
            'acquisition_value'             => $row[11],
            'current_cost'                  => $row[11],
            'commercial_useful_life_month'  => $commercialUsefulLife,
            'commercial_accum_depre'        => 0,
            'commercial_nbv'                => $row[11],
            'fiscal_useful_life_month'      => $fiscalUsefulLife,
            'fiscal_accum_depre'            => 0,
            'fiscal_nbv'                    => $row[11],
            'company_id'                    => session('active_company_id'),
            'status'                        => 'Active',
            'asset_type'                    => 'LVA',
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => [
                Rule::unique('assets', 'asset_number')->where('company_id', $this->companyId),
            ],
            '1' => [
                'required',
                Rule::exists('asset_names', 'name')->where('company_id', $this->companyId),
            ],
            '2' => 'required|string|max:255',
            '3' => 'nullable|string|max:255',
            '4' => 'nullable|string|max:255',
            '5' => 'nullable|string|max:255',
            '6' => 'nullable|string|max:255',
            '7' => [
                'required',
                Rule::exists('locations', 'name')->where('company_id', $this->companyId),
            ],
            '8' => [
                'required',
                Rule::exists('departments', 'name')->where('company_id', $this->companyId),
            ],
            '9' => 'required',
            '10' => 'required',
            '11' => 'required',
        ];
    }
}
