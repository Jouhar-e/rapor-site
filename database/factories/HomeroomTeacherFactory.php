<?php

namespace Database\Factories;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\HomeroomTeacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HomeroomTeacher>
 */
class HomeroomTeacherFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'class_id' => Classes::factory(),
            'academic_year_id' => AcademicYear::factory(),
        ];
    }
}
