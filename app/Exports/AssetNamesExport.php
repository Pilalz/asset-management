<?php

namespace App\Exports;

use App\Models\AssetName;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AssetNamesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return AssetName::withoutGlobalScope(CompanyScope::class)
            ->with(['assetSubClass'])
            ->where('company_id', session('active_company_id'));
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
        ];
    }
}
