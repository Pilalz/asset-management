<?php

namespace App\Exports;

use App\Models\AssetName;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class AssetNamesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return AssetName::with(['assetSubClass']);
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
