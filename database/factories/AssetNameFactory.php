<?php

namespace Database\Factories;

use App\Models\AssetName;
use App\Models\AssetSubClass;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetNameFactory extends Factory
{
    protected $model = AssetName::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'grouping' => $this->faker->word(),
            'commercial' => $this->faker->numberBetween(1, 10),
            'fiscal' => $this->faker->numberBetween(1, 10),
            'company_id' => Company::factory(),
            'sub_class_id' => AssetSubClass::factory(),
        ];
    }
}
