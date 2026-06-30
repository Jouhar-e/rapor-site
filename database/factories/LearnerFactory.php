<?php

namespace Database\Factories;

use App\Models\Learner;
use App\Models\Program;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Learner>
 */
class LearnerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'program_id' => Program::factory(),
            'nis' => fake()->unique()->numerify('##########'),
            'nisn' => fake()->unique()->numerify('##########'),
            'name' => fake()->name(),
            'gender' => fake()->randomElement(['L', 'P']),
            'birth_place' => fake()->city(),
            'birth_date' => fake()->date(),
            'address' => fake()->address(),
            'status' => 'aktif',
            'religion' => fake()->randomElement(['Islam', 'Kristen', 'Katolik', 'Hindu', 'Buddha']),
            'child_order' => fake()->numberBetween(1, 5),
            'phone' => fake()->phoneNumber(),
            'admission_date' => fake()->date(),
            'admission_class' => fake()->word(),
            'admission_status' => fake()->randomElement(['baru', 'pindahan']),
            'father_name' => fake()->name('male'),
            'father_job' => fake()->jobTitle(),
            'mother_name' => fake()->name('female'),
            'mother_job' => fake()->jobTitle(),
            'guardian_name' => fake()->name(),
            'guardian_job' => fake()->jobTitle(),
            'report_number' => fake()->unique()->numerify('RPT-#####'),
        ];
    }
}
