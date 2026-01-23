<?php

namespace Database\Factories;

use App\Models\AssetSubClass;
use App\Models\AssetClass;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetSubClassFactory extends Factory
{
    protected $model = AssetSubClass::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'class_id' => AssetClass::factory(),
            'company_id' => Company::factory(),
        ];
    }
}
