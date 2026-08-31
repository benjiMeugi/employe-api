<?php

namespace Database\Factories;

use App\Models\PayslipLine;
use App\Models\Payslip;
use App\Models\PayrollLineType;
use Illuminate\Database\Eloquent\Factories\Factory;

class PayslipLineFactory extends Factory
{
    protected $model = PayslipLine::class;

    public function definition(): array
    {
        return [
            'payslip_id' => Payslip::factory(),
            'payroll_line_type_id' => PayrollLineType::factory(),
            'calculation_base' => $this->faker->randomFloat(2, 2000, 5000),
            'rate' => $this->faker->randomFloat(2, 1, 100),
            'amount' => $this->faker->randomFloat(2, 50, 1000),
        ];
    }
}
