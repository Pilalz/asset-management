<?php

namespace App\Imports;

use App\Models\AssetSubClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetSubClassesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AssetSubClass([
            'name'    => $row['name'],
            'class_id'    => $row['class_id'],
            'company_id'   => session('active_company_id'),
        ]);
    }
}
