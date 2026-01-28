<?php

namespace App\Exports;

use App\Models\Location;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class LocationsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Location::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', session('active_company_id'));
    }

    public function headings(): array
    {
        return [
            'ID Lokasi',
            'Nama Lokasi',
            'Deskripsi Lokasi',
        ];
    }

    public function map($location): array
    {
        return [
            $location->id,
            $location->name,
            $location->description,
        ];
    }
}
