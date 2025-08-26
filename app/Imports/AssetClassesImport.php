<?php

namespace App\Imports;

use App\Models\AssetClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AssetClassesImport implements ToModel, WithStartRow
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
        return new AssetClass([
            'name'       => $row[0],
            'obj_id'     => $row[1],
            'obj_acc'    => $row[2],
            'company_id' => session('active_company_id'),
        ]);
    }
}
