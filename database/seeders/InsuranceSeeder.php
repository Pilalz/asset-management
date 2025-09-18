<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InsuranceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('insurances')->insert([
            [
                'polish_no' => 'INS25901822',
                'start_date' => '2025-08-06',
                'end_date' => '2030-08-06',
                'instance_name' => '',
                'annual_premium' => '',
                'schedule' => '',
                'next_payment' => '',
                'status' => '',
                'company_id' => '2',
            ],
            [
                'polish_no' => 'INS974235',
                'start_date' => '2025-08-06',
                'end_date' => '2030-08-06',
                'instance_name' => '',
                'annual_premium' => '',
                'schedule' => '',
                'next_payment' => '',
                'status' => '',
                'company_id' => '2',
            ],
        ]);

        DB::table('detail_insurances')->insert([
            [
                'insurance_id' => '1',
                'asset_id' => '37',
            ],
            [
                'insurance_id' => '1',
                'asset_id' => '38',
            ],
            [
                'insurance_id' => '2',
                'asset_id' => '35',
            ],
        ]);
    }
}
