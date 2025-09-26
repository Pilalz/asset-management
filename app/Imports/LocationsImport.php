<?php

namespace App\Imports;

use App\Models\Location;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LocationsImport implements ToModel, WithStartRow, WithValidation 
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
        return new Location([
            'name'                  => $row[0],
            'description'           => $row[1],        
            'company_id'            => session('active_company_id'),
        ]);
    }

    public function rules(): array
    {
        return [
            '0' => 'required|string|max:255|unique:locations,name',
            '1' => 'nullable|string|max:255',
        ];
    }
}
