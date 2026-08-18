<?php

namespace Database\Factories;

use App\Models\LeaveBalance;
use App\Models\AbsenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveBalanceFactory extends Factory
{
    protected $model = LeaveBalance::class;

    public function definition(): array
    {
        return [
            'employee_id' => 1,
            'absence_type_id' => AbsenceType::factory(),
            'year' => 2026,
            'cumulative_acquired_days' => 30.00,
            'consumed_days' => $this->faker->randomFloat(2, 0, 15),
            'expired_days' => 0.00,
            'available_balance' => $this->faker->randomFloat(2, 15, 30),
        ];
    }
}
