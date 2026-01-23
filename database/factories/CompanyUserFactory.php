<?php

namespace Database\Factories;

use App\Models\Company;
use App\Models\User;
use App\Models\CompanyUser;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyUserFactory extends Factory
{
    protected $model = CompanyUser::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'company_id' => Company::factory(),
            'role' => $this->faker->randomElement(['admin', 'user', 'viewer']),
        ];
    }
}
