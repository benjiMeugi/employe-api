<?php

namespace Database\Factories;

use App\Models\AbsenceRequest;
use App\Models\AbsenceType;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceRequestFactory extends Factory
{
    protected $model = AbsenceRequest::class;

    public function definition(): array
    {
        return [
            'employee_id' => 1,
            'absence_type_id' => AbsenceType::factory(),
            'requested_start_date' => $this->faker->date(),
            'requested_end_date' => $this->faker->date(),
            'requested_days_count' => $this->faker->numberBetween(1, 10),
            'reason' => $this->faker->sentence(),
            'status' => $this->faker->randomElement(['Pending', 'Approved', 'Rejected']),
            'approver_id' => null,
            'decision_datetime' => null,
            'decision_comment' => null,
            'is_deductible' => $this->faker->boolean(),
        ];
    }
}
