<?php

namespace App\Exports;

use App\Models\Department;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class DepartmentsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    /**
    * @return \Illuminate\Support\Collection
    */
    public function query()
    {
        return Department::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'));
    }

    public function headings(): array
    {
        return [
            'ID Department',
            'Nama Department',
            'Deskripsi Department',
        ];
    }

    public function map($department): array
    {
        return [
            $department->id,
            $department->name,
            $department->description,
        ];
    }
}
