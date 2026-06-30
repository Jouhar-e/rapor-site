<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\Learner;
use App\Models\LearnerExtracurricular;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LearnerExtracurricular>
 */
class LearnerExtracurricularFactory extends Factory
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
            'extracurricular_id' => Extracurricular::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'predicate' => fake()->optional()->randomElement(['A', 'B', 'C', 'D']),
            'description' => fake()->optional()->sentence(),
        ];
    }
}
