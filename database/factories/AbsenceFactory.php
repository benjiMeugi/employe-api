<?php

namespace Database\Factories;

use App\Models\Absence;
use App\Models\AbsenceType;
use App\Models\AbsenceRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

class AbsenceFactory extends Factory
{
    protected $model = Absence::class;

    public function definition(): array
    {
        return [
            'absence_type_id' => AbsenceType::factory(),
            'absence_request_id' => AbsenceRequest::factory(),
            'start_date' => $this->faker->date(),
            'end_date' => $this->faker->date(),
            'days_count' => $this->faker->numberBetween(1, 10),
            'is_deductible' => $this->faker->boolean(),
        ];
    }
}
