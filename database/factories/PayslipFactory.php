<?php

namespace Database\Factories;

use App\Models\Payslip;
use App\Models\Contract;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayslipFactory extends Factory
{
    protected $model = Payslip::class;

    public function definition(): array
    {
        return [
            'employee_id' => 1,
            'contract_id' => Contract::factory(),
            'period' => $this->faker->date('Y-m'), // Exemple : 2026-08
            'issue_date' => $this->faker->date(),
            'gross_salary' => $this->faker->randomFloat(2, 2000, 5000),
            'total_earnings' => $this->faker->randomFloat(2, 2000, 5000),
            'total_deductions' => $this->faker->randomFloat(2, 100, 800),
            'net_pay' => $this->faker->randomFloat(2, 1800, 4200),
            'status' => $this->faker->randomElement(['Pending', 'Validated', 'Paid']),
        ];
    }
}
