<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Learner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ClassLearner>
 */
class ClassLearnerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'learner_id' => Learner::factory(),
            'class_id' => Classes::factory(),
            'academic_year_id' => AcademicYear::factory(),
        ];
    }
}
