<?php

namespace Database\Factories;

use App\Models\LeaveCredit;
use App\Models\AbsenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeaveCreditFactory extends Factory
{
    protected $model = LeaveCredit::class;

    public function definition(): array
    {
        return [
            'employee_id' => 1,
            'absence_type_id' => AbsenceType::factory(),
            'period' => $this->faker->date('Y-m'),
            'acquired_days' => 2.5, // 2.5 jours par mois généralement
            'acquisition_date' => $this->faker->date(),
            'expiration_date' => $this->faker->date(),
        ];
    }
}
