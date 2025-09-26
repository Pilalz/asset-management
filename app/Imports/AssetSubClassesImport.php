<?php

namespace App\Imports;

use App\Models\AssetClass;
use App\Models\AssetSubClass;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Cache;

class AssetSubClassesImport implements ToModel, WithStartRow, WithValidation
{
    private $assetClasses;

    public function __construct()
    {
        // 2. Ambil semua data AssetClass sekali saja untuk menghindari query berulang di dalam loop
        $this->assetClasses = AssetClass::all()->keyBy('name');
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
        $assetClassName = $row[0];
        $assetClass = $this->assetClasses->get($assetClassName);

        return new AssetSubClass([
            'name'    => $row[1],
            'class_id'    => $assetClass ? $assetClass->id : null,
            'company_id'   => session('active_company_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|exists:asset_classes,name',
            '1' => 'required|string|max:255|unique:asset_sub_classes,name',
        ];
    }
}
