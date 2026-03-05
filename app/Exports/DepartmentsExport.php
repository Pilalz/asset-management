<?php

namespace App\Exports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\Exportable;

class DepartmentsExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    public function query()
    {
        return Department::query();
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
