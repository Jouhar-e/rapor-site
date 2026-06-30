<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\Learner;
use App\Models\Semester;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Grade>
 */
class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'learner_id' => Learner::factory(),
            'subject_id' => Subject::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'task_score' => fake()->optional()->randomFloat(2, 0, 100),
            'pts_score' => fake()->optional()->randomFloat(2, 0, 100),
            'pas_score' => fake()->optional()->randomFloat(2, 0, 100),
            'practice_score' => fake()->optional()->randomFloat(2, 0, 100),
            'final_score' => fake()->optional()->randomFloat(2, 0, 100),
            'predicate' => fake()->optional()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'description' => fake()->optional()->sentence(),
            'competency_description' => fake()->optional()->sentence(),
            'status' => 'draft',
        ];
    }
}
