<?php

namespace Database\Factories;

use App\Models\ContractType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractTypeFactory extends Factory
{
    protected $model = ContractType::class;

    public function definition(): array
    {
        return [
            'code' => $this->faker->unique()->randomElement(['CDI', 'CDD', 'STAGE', 'INTERIM']),
            'label' => $this->faker->randomElement(['Contrat à Durée Indéterminée', 'Contrat à Durée Déterminée', 'Stage Professionnel', 'Mission Intérim']),
            'is_fixed_term' => $this->faker->boolean(),
            'max_duration_months' => $this->faker->optional()->numberBetween(3, 24),
        ];
    }
}
