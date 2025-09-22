<?php

namespace App\Exports;

use App\Models\AssetName;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AssetNamesExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return AssetName::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'))
                ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Asset Class',
            'Asset Sub Class',
            'Asset Name',
            'Asset Grouping',
            'Commercial Life',
            'Fiscal Life',
            'Cost',
            'LVA',
        ];
    }

    public function map($assetName): array
    {
        return [
            $assetName->id,
            $assetName->assetSubClass->assetClass->name,
            $assetName->assetSubClass->name,
            $assetName->name,
            $assetName->grouping,
            $assetName->commercial,
            $assetName->fiscal,
            $assetName->cost,
            $assetName->lva,
        ];
    }
}
