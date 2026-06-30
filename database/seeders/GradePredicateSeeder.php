<?php

namespace Database\Seeders;

use App\Models\GradePredicate;
use Illuminate\Database\Seeder;

class GradePredicateSeeder extends Seeder
{
    public function run(): void
    {
        GradePredicate::create([
            'min_score' => 90,
            'max_score' => 100,
            'predicate' => 'A',
            'description' => 'Memiliki kemampuan sangat baik dalam memahami materi.',
        ]);

        GradePredicate::create([
            'min_score' => 75,
            'max_score' => 89,
            'predicate' => 'B',
            'description' => 'Memiliki kemampuan baik dalam memahami materi.',
        ]);

        GradePredicate::create([
            'min_score' => 60,
            'max_score' => 74,
            'predicate' => 'C',
            'description' => 'Memiliki kemampuan cukup dalam memahami materi.',
        ]);

        GradePredicate::create([
            'min_score' => 0,
            'max_score' => 59,
            'predicate' => 'D',
            'description' => 'Memerlukan bimbingan lebih lanjut.',
        ]);
    }
}
