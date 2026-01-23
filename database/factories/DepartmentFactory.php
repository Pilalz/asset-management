<?php

namespace Database\Factories;

use App\Models\Department;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
