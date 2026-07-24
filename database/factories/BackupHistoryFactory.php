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
            'filename' => 'backup-db-'.now()->format('Y-m-d-H-i-s').'.sql',
            'file_size' => fake()->optional()->numberBetween(1024, 10485760),
            'type' => 'database',
            'status' => 'completed',
            'started_at' => fake()->optional()->dateTimeThisMonth(),
            'completed_at' => fake()->optional()->dateTimeThisMonth(),
            'notes' => null,
            'created_by' => fake()->optional()->numberBetween(1, 10),
        ];
    }
}
