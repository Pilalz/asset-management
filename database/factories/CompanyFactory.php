<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'currency' => $this->faker->randomElement(['IDR', 'USD', 'SGD']),
            'phone' => $this->faker->phoneNumber(),
            'fax' => $this->faker->phoneNumber(),
            'address' => $this->faker->address(),
            'code' => $this->faker->bothify('??-####'),
            'alias' => $this->faker->word(),
            'owner_id' => User::factory(),
        ];
    }
}
