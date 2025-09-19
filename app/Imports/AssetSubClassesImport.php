<?php

namespace App\Imports;

use App\Models\AssetSubClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AssetSubClassesImport implements ToModel, WithStartRow, WithValidation
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
        return new AssetSubClass([
            'name'    => $row[1],
            'class_id'    => $row[0],
            'company_id'   => session('active_company_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|max:255',
            '1' => 'required|string|max:255',
        ];
    }
}
