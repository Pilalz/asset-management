<?php

namespace Database\Factories;

use App\Models\Insurance;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

class InsuranceFactory extends Factory
{
    protected $model = Insurance::class;

    public function definition(): array
    {
        return [
            'asset_id' => Asset::factory(),
            'insurance_type' => $this->faker->randomElement(['Liability', 'Property', 'Comprehensive']),
            'coverage_amount' => $this->faker->numberBetween(5000000, 100000000),
            'premium_amount' => $this->faker->numberBetween(100000, 5000000),
            'start_date' => now(),
            'end_date' => now()->addYear(),
        ];
    }
}
