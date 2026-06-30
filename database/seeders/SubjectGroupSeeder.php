<?php

namespace Database\Seeders;

use App\Models\SubjectGroup;
use Illuminate\Database\Seeder;

class SubjectGroupSeeder extends Seeder
{
    public function run(): void
    {
        $groups = [
            ['name' => 'Kelompok Mata Pelajaran Umum', 'sort_order' => 1],
            ['name' => 'Kelompok Pemberdayaan', 'sort_order' => 2],
            ['name' => 'Kelompok Keterampilan', 'sort_order' => 3],
            ['name' => 'Muatan Lokal', 'sort_order' => 4],
        ];

        foreach ($groups as $group) {
            SubjectGroup::updateOrCreate(
                ['name' => $group['name']],
                ['sort_order' => $group['sort_order'], 'is_active' => true],
            );
        }
    }
}
