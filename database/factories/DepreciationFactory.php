<?php

namespace Database\Factories;

use App\Models\Depreciation;
use App\Models\Asset;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepreciationFactory extends Factory
{
    protected $model = Depreciation::class;

    public function definition(): array
    {
        $monthly_depre = $this->faker->numberBetween(100000, 1000000);
        $accumulated_depre = $this->faker->numberBetween(0, 10000000);
        
        return [
            'asset_id' => Asset::factory(),
            'type' => $this->faker->randomElement(['commercial', 'fiscal']),
            'depre_date' => Carbon::now(),
            'monthly_depre' => $monthly_depre,
            'accumulated_depre' => $accumulated_depre,
            'book_value' => $this->faker->numberBetween($accumulated_depre, 50000000),
            'company_id' => Company::factory(),
        ];
    }
}
