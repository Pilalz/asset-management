<?php

namespace App\Exports;

use App\Models\AssetClass;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AssetClassesExport implements FromQuery, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */

    use Exportable;

    public function query()
    {
        return AssetClass::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'));
    }

    public function headings(): array
    {
        return [
            'ID',
            'Nama Asset Class',
            'Object ID',
            'Object Acc',
        ];
    }

    public function map($assetClass): array
    {
        return [
            $assetClass->id,
            $assetClass->name,
            $assetClass->obj_id,
            $assetClass->obj_acc,
        ];
    }
}
