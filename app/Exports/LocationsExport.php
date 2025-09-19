<?php

namespace App\Exports;

use App\Models\Location;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LocationsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Location::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'))
                ->get();
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
