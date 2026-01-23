<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\AssetName;
use App\Models\Company;
use App\Models\Location;
use App\Models\Department;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        $acquisitionValue = $this->faker->numberBetween(5000000, 100000000);
        $usefulLifeMonths = $this->faker->numberBetween(12, 120);

        return [
            'asset_number' => 'AST-' . $this->faker->unique()->numerify('######'),
            'asset_name_id' => AssetName::factory(),
            'asset_type' => $this->faker->randomElement(['FA', 'LVA', 'Arrival']),
            'status' => $this->faker->randomElement(['Active', 'Sold', 'Disposal', 'Onboard']),
            'description' => $this->faker->sentence(),
            'detail' => $this->faker->text(100),
            'pareto' => $this->faker->randomElement(['A', 'B', 'C']),
            'unit_no' => $this->faker->numerify('###'),
            'user' => $this->faker->name(),
            'sn' => $this->faker->bothify('??-####-??'),
            'production_year' => $this->faker->year(),
            'po_no' => 'PO-' . $this->faker->numerify('######'),
            'location_id' => Location::factory(),
            'department_id' => Department::factory(),
            'quantity' => $this->faker->numberBetween(1, 10),
            'capitalized_date' => Carbon::now()->subMonths(24),
            'start_depre_date' => Carbon::now()->subMonths(24),
            'acquisition_value' => $acquisitionValue,
            'current_cost' => $acquisitionValue,
            'commercial_useful_life_month' => $usefulLifeMonths,
            'commercial_accum_depre' => 0,
            'commercial_nbv' => $acquisitionValue,
            'fiscal_useful_life_month' => $usefulLifeMonths - 12,
            'fiscal_accum_depre' => 0,
            'fiscal_nbv' => $acquisitionValue,
            'remaks' => $this->faker->sentence(),
            'company_id' => Company::factory(),
        ];
    }
}
