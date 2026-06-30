<?php

namespace Database\Factories;

use App\Models\Extracurricular;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Extracurricular>
 */
class ExtracurricularFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code' => fake()->unique()->bothify('EKS-####'),
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
