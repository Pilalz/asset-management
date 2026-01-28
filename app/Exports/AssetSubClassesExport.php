<?php

namespace App\Exports;

use App\Models\AssetSubClass;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AssetSubClassesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return AssetSubClass::withoutGlobalScope(CompanyScope::class)
            ->with(['assetClass'])
            ->where('company_id', session('active_company_id'));
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
