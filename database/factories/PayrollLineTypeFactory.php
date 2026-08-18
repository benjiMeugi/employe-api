<?php

namespace Database\Factories;

use App\Models\PayrollLineType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayrollLineTypeFactory extends Factory
{
    protected $model = PayrollLineType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->lexify('???-###'),
            'label' => $this->faker->sentence(3),
            'nature' => $this->faker->randomElement(['Earning', 'Deduction']),
            'calculation_mode' => $this->faker->randomElement(['Rate', 'FixedAmount', 'Formula']),
            'is_taxable' => $this->faker->boolean(),
            'is_subject_to_contributions' => $this->faker->boolean(),
            'is_employer_contribution' => $this->faker->boolean(),
        ];
    }
}
