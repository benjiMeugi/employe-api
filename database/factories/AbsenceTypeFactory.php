<?php

namespace Database\Factories;

use App\Models\AbsenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceTypeFactory extends Factory
{
    protected $model = AbsenceType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomElement(['CP', 'CSS', 'MAL', 'MAT']),
            'label' => $this->faker->randomElement(['Congés Payés', 'Congé Sans Solde', 'Maladie', 'Maternité']),
            'is_paid' => $this->faker->boolean(),
            'is_cumulative' => $this->faker->boolean(),
            'max_cumulative_years' => 2,
            'day_cap' => 30,
            'expiration_delay_months' => 12,
            'requires_supporting_document' => $this->faker->boolean(),
        ];
    }
}
