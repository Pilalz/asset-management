<?php

namespace App\Imports;

use App\Models\AssetSubClass;
use App\Models\AssetName;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Cache;

class AssetNamesImport implements ToModel, WithStartRow, WithValidation
{
    private $assetSubClasses;

    public function __construct()
    {
        // 2. Ambil semua data AssetClass sekali saja untuk menghindari query berulang di dalam loop
        $this->assetSubClasses = AssetSubClass::all()->keyBy('name');
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

        $assetSubClassName = $row[0];
        $assetSubClass = $this->assetSubClasses->get($assetSubClassName);

        return new AssetName([
            'sub_class_id'  => $assetSubClass ? $assetSubClass->id : null,
            'name'          => $row[1],
            'grouping'      => $row[2],
            'commercial'    => $row[3],
            'fiscal'        => $row[4],
            'cost'          => $row[5],
            'lva'           => $row[6],
            'company_id'    => session('active_company_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|exists:asset_sub_classes,name',
            '1' => 'required|string|max:255',
            '2' => 'required',
            '3' => 'required',
            '4' => 'required',
            '5' => 'required',
            '6' => 'required',

        ];
    }
}
