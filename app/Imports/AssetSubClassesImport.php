<?php

namespace App\Imports;

use App\Models\AssetSubClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AssetSubClassesImport implements ToModel, WithStartRow
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
        return new AssetSubClass([
            'name'    => $row[1],
            'class_id'    => $row[0],
            'company_id'   => session('active_company_id'),
        ]);
    }
}
