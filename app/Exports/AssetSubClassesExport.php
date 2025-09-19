<?php

namespace App\Exports;

use App\Models\AssetSubClass;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetSubClassesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssetSubClass::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'))
                ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asset Class',
            'Asset Sub Class',
        ];
    }

    public function map($assetSubClass): array
    {
        return [
            $assetSubClass->id,
            $assetSubClass->assetClass->name,
            $assetSubClass->name,
        ];
    }
}
