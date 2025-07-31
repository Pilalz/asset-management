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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Mantimin',
                'description' => 'Kalimantan Selatan',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('departments')->insert([
            [
                'name' => 'HRGA',
                'description' => 'HRGA',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'PLANT',
                'description' => 'PLANT',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('asset_classes')->insert([
            [
                'name' => 'Buildings',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Infrastructure',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('asset_sub_classes')->insert([
            [
                'class_id' => '1',
                'name' => 'Buildings',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'class_id' => '2',
                'name' => 'Container & Ramps',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
