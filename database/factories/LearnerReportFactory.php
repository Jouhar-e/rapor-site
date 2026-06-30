<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Learner;
use App\Models\LearnerReport;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

class LearnerReportFactory extends Factory
{
    protected $model = LearnerReport::class;

    public function definition(): array
    {
        return [
            'learner_id' => Learner::factory(),
            'academic_year_id' => AcademicYear::factory(),
            'semester_id' => Semester::factory(),
            'report_number' => fake()->unique()->numerify('RPT-#####'),
            'issued_date' => fake()->date(),
            'status' => 'draft',
        ];
    }
}
