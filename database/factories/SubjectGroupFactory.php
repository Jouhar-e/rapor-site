<?php

namespace Database\Factories;

use App\Models\SubjectGroup;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubjectGroupFactory extends Factory
{
    protected $model = SubjectGroup::class;

    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'description' => fake()->sentence(),
            'sort_order' => fake()->numberBetween(1, 10),
            'is_active' => true,
        ];
    }
}
