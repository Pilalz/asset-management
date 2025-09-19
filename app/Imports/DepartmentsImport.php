<?php

namespace App\Imports;

use App\Models\Department;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class DepartmentsImport implements ToModel, WithStartRow, WithValidation 
{
    public function startRow(): int
    {
        return 3;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {

        return new Department([
            'name'                  => $row[0],
            'description'           => $row[1],        
            'company_id'            => session('active_company_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|max:255',
            '1' => 'nullable|string|max:255',
        ];
    }
}
