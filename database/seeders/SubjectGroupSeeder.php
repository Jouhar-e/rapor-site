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
            ['name' => 'Kelompok Pemberdayaan dan Keterampilan Berbasis Profil Pelajar Pancasila', 'sort_order' => 2],
            ['name' => 'Muatan Lokal', 'sort_order' => 3],
        ];

        foreach ($groups as $group) {
            SubjectGroup::updateOrCreate(
                ['name' => $group['name']],
                ['sort_order' => $group['sort_order'], 'is_active' => true],
            );
        }
    }
}
