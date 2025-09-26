<?php

namespace App\Imports;

use App\Models\AssetClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetClassesImport implements ToModel, WithStartRow, WithValidation
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
        return new AssetClass([
            'name'       => $row[0],
            'obj_id'     => $row[1],
            'obj_acc'    => $row[2],
            'company_id' => session('active_company_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|max:255|unique:asset_classes,name',
            '1' => 'required|max:255|unique:asset_classes,obj_id',
            '2' => 'required|string|max:255',
        ];
    }
}
