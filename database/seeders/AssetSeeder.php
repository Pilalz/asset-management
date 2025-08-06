<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('assets')->insert([
            [
                'asset_number' => 'FA012025080001',
                'asset_name_id' => '1',
                'status' => 'active',
                'description' => 'Adjustable Table',
                'location_id' => '2',
                'department_id' => '2',
                'quantity' => '1',
                'capitalized_date' => '2025-08-06',
                'start_depre_date' => '2025-08-01',
                'acquisition_value' => '1000000',
                'current_cost' => '1000000',
                'useful_life_month' => '48',
                'accum_depre' => '0',
                'net_book_value' => '1000000',
                'company_id' => '2',
            ],
        ]);
    }
}
