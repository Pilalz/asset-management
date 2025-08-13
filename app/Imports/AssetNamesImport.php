<?php

namespace App\Imports;

use App\Models\AssetName;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssetNamesImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new AssetName([
            'sub_class_id'    => $row['sub_class_id'],
            'name'    => $row['name'],
            'code'    => $row['code'],
            'commercial'    => $row['commercial'],
            'fiscal'    => $row['fiscal'],
            'cost'    => $row['cost'],
            'lva'    => $row['lva'],
            'company_id'   => session('active_company_id'),
        ]);
    }
}
