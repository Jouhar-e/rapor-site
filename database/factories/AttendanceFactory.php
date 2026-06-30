<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Learner;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Attendance>
 */
class AttendanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'learner_id' => Learner::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'sick' => fake()->numberBetween(0, 10),
            'permission' => fake()->numberBetween(0, 10),
            'absent' => fake()->numberBetween(0, 10),
        ];
    }
}
