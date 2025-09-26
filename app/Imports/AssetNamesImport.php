<?php

namespace App\Imports;

use App\Models\AssetSubClass;
use App\Models\AssetName;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Facades\Cache;

class AssetNamesImport implements ToModel, WithStartRow, WithValidation
{
    private $assetSubClasses;
    private $companyId;

    public function __construct()
    {
        $this->companyId = session('active_company_id');
        $this->assetSubClasses = AssetSubClass::where('company_id', $this->companyId)
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
            '0' => [
                'required',
                Rule::exists('asset_sub_classes', 'name')->where('company_id', $this->companyId),
            ],
            '1' => [
                'required',
                'string',
                'max:255',
                Rule::unique('asset_names', 'name')->where('company_id', $this->companyId),
            ],
            '2' => 'required',
            '3' => 'required',
            '4' => 'required',
            '5' => 'required',
            '6' => 'required',

        ];
    }
}
