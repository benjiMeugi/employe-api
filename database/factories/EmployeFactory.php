<?php

namespace Database\Factories;

use App\Models\Employe;
use App\Models\Position;
use App\Models\Title;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Employe>
 */
class EmployeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $position = Position::query()
            ->inRandomOrder()
            ->first();

        return [
            'registration_number' => fake()->unique()->numerify('EMP-######'),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'birth_date' => fake()->dateTimeBetween('-60 years', '-22 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(Employe::$GENDER_OPTIONS),
            'hire_date' => fake()->dateTimeBetween('-15 years', 'now')->format('Y-m-d'),
            'status' => fake()->boolean(90),
            'professional_email' => fake()->unique()->safeEmail(),
            'personal_email' => fake()->unique()->safeEmail(),
            'phone_number1' => fake()->phoneNumber(),
            'phone_number2' => fake()->optional()->phoneNumber(),
            'title_id' => Title::query()->inRandomOrder()->value('id'),
            'classification_id' => $position->classification_id,
            'position_id' => $position->id,
        ];
    }
}
