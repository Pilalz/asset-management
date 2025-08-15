<?php

namespace App\Imports;

use App\Models\AssetClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetClassesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AssetClass([
            'name'    => $row['name'],
            'obj_id'    => $row['obj_id'],
            'obj_acc'    => $row['obj_acc'],
            'company_id'   => session('active_company_id'),
        ]);
    }
}
