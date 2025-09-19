<?php

namespace App\Exports;

use App\Models\Department;
use App\Scopes\CompanyScope;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DepartmentsExport implements FromCollection, WithHeadings, WithMapping
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Department::withoutGlobalScope(CompanyScope::class)
                ->where('company_id', session('active_company_id'))
                ->get();
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
