<?php

namespace Database\Factories;

use App\Models\Contract;
use App\Models\ContractType;
use Illuminate\Database\Eloquent\Factories\Factory;

class ContractFactory extends Factory
{
    protected $model = Contract::class;

    public function definition(): array
    {
        return [
            'employee_id' => 1, 
            'contract_type_id' => ContractType::factory(),
            'start_date' => $this->faker->date('Y-m-d', 'now'),
            'end_date' => null,
            'pay_frequency' => 'Monthly',
            'base_salary' => $this->faker->randomFloat(2, 1500, 7500),
            'status' => 'Active',
        ];
    }
}
