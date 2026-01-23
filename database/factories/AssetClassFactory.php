<?php

namespace Database\Factories;

use App\Models\AssetClass;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetClassFactory extends Factory
{
    protected $model = AssetClass::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'obj_id' => $this->faker->bothify('###-?????'),
            'obj_acc' => $this->faker->bothify('?-?-???-###'),
            'company_id' => Company::factory(),
        ];
    }
}
