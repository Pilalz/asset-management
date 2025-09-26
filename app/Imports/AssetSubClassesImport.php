<?php

namespace App\Imports;

use App\Models\AssetClass;
use App\Models\AssetSubClass;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Cache;

class AssetSubClassesImport implements ToModel, WithStartRow, WithValidation
{
    private $assetClasses;
    private $companyId;

    public function __construct()
    {
        $this->companyId = session('active_company_id');
        $this->assetClasses = AssetClass::where('company_id', $this->companyId)
            ->get()
            ->keyBy('name');
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
            '0' => ['required', 'string', Rule::exists('asset_classes', 'name')->where('company_id', $this->companyId)],
            '1' => ['required', 'string', 'max:255', Rule::unique('asset_sub_classes', 'name')->where('company_id', $this->companyId)],
        ];
    }
}
