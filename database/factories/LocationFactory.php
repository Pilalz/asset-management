<?php

namespace Database\Factories;

use App\Models\Location;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class LocationFactory extends Factory
{
    protected $model = Location::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
