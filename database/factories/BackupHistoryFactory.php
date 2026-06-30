<?php

namespace Database\Factories;

use App\Models\BackupHistory;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BackupHistory>
 */
class BackupHistoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'file_name' => fake()->word().'.sql',
            'file_size' => fake()->optional()->numberBetween(1024, 10485760),
            'backup_type' => fake()->optional()->randomElement(['full', 'manual', 'automated']),
            'created_by' => fake()->optional()->numberBetween(1, 10),
        ];
    }
}
