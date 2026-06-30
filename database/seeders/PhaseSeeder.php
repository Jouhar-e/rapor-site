<?php

namespace Database\Seeders;

use App\Models\Phase;
use Illuminate\Database\Seeder;

class PhaseSeeder extends Seeder
{
    public function run(): void
    {
        $phases = [
            ['code' => 'A', 'name' => 'Fase A', 'description' => 'Fase A - Kelas 1-2'],
            ['code' => 'B', 'name' => 'Fase B', 'description' => 'Fase B - Kelas 3-4'],
            ['code' => 'C', 'name' => 'Fase C', 'description' => 'Fase C - Kelas 5-6'],
            ['code' => 'D', 'name' => 'Fase D', 'description' => 'Fase D - Kelas 7-9'],
            ['code' => 'E', 'name' => 'Fase E', 'description' => 'Fase E - Kelas 10'],
            ['code' => 'F', 'name' => 'Fase F', 'description' => 'Fase F - Kelas 11-12'],
        ];

        foreach ($phases as $phase) {
            Phase::updateOrCreate(['code' => $phase['code']], $phase);
        }
    }
}
