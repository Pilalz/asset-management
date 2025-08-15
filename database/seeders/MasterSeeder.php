<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('locations')->insert([
            [
                'name' => 'Tuhup',
                'description' => 'Kalimantan Selatan',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mantimin',
                'description' => 'Kalimantan Selatan',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tapin',
                'description' => 'Kalimantan Selatan',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Kuaro',
                'description' => 'Kalimantan Selatan',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('departments')->insert([
            [
                'name' => 'HRGA',
                'description' => 'HRGA',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PLANT',
                'description' => 'PLANT',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
