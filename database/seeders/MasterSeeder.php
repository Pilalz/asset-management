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

        DB::table('asset_classes')->insert([
            [
                'name' => 'Buildings',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Infrastructure',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('asset_sub_classes')->insert([
            [
                'class_id' => '1',
                'name' => 'Buildings',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'class_id' => '2',
                'name' => 'Container & Ramps',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);

        DB::table('asset_names')->insert([
            [
                'sub_class_id' => '1',
                'name' => 'Build Permanent-Office',
                'code' => 'BPO',
                'commercial' => '20',
                'fiscal' => '20',
                'cost' => '1000',
                'lva' => '1000',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'sub_class_id' => '2',
                'name' => 'Container & Ramps',
                'code' => 'CNR',
                'commercial' => '8',
                'fiscal' => '10',
                'cost' => '1000',
                'lva' => '1000',
                'company_id' => '2',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
