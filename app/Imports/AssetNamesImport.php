<?php

namespace App\Imports;

use App\Models\AssetName;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AssetNamesImport implements ToModel, WithStartRow
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
        return new AssetName([
            'sub_class_id'  => $row[0],
            'name'          => $row[1],
            'grouping'      => $row[2],
            'commercial'    => $row[3],
            'fiscal'        => $row[4],
            'cost'          => $row[5],
            'lva'           => $row[6],
            'company_id'    => session('active_company_id'),
        ]);
    }
}
