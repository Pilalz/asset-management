<?php

namespace App\Exports;

use App\Models\AssetClass;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetClassesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssetClass::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'))
                ->get();
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
