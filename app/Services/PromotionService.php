<?php

namespace App\Services;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Learner;
use App\Models\PromotionMapping;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class PromotionService
{
    public function __construct(
        protected ClassLearner $classLearner,
        protected PromotionMapping $promotionMapping,
    ) {}

    public function getActiveClasses(AcademicYear $year): Collection
    {
        return Classes::query()
            ->whereHas('classLearners', function ($query) use ($year): void {
                $query->where('academic_year_id', $year->id);
            })
            ->orWhere('status', 'aktif')
            ->orderBy('name')
            ->get();
    }

    public function getPromotionCandidates(Classes $sourceClass, ?AcademicYear $year = null): Collection
    {
        return Learner::query()
            ->whereHas('classLearners', function ($query) use ($sourceClass, $year): void {
                $query->where('class_id', $sourceClass->id)
                    ->when($year, fn ($q) => $q->where('academic_year_id', $year->id));
            })
            ->where('status', 'aktif')
            ->get();
    }

    public function promoteLearner(
        Learner $learner,
        Classes $destinationClass,
        AcademicYear $newYear,
    ): ClassLearner {
        return $this->classLearner->create([
            'learner_id' => $learner->id,
            'class_id' => $destinationClass->id,
            'academic_year_id' => $newYear->id,
        ]);
    }

    public function generatePromotions(PromotionMapping $mapping): Collection
    {
        $candidates = $this->getPromotionCandidates($mapping->sourceClass, $mapping->academicYear);

        return $candidates->map(function (Learner $learner) use ($mapping) {
            return [
                'learner' => $learner,
                'source_class' => $mapping->sourceClass,
                'destination_class' => $mapping->destinationClass,
            ];
        });
    }

    public function executePromotions(AcademicYear $sourceYear, AcademicYear $destYear): array
    {
        $mappings = PromotionMapping::query()
            ->where('academic_year_id', $sourceYear->id)
            ->whereNull('promoted_at')
            ->get();

        $results = [];

        DB::transaction(function () use ($mappings, $destYear, &$results): void {
            foreach ($mappings as $mapping) {
                $candidates = $this->getPromotionCandidates($mapping->sourceClass, $mapping->academicYear);
                $count = 0;

                foreach ($candidates as $learner) {
                    $this->promoteLearner($learner, $mapping->destinationClass, $destYear);
                    $count++;
                }

                $mapping->promoted_at = now();
                $mapping->save();

                $results[] = [
                    'mapping' => $mapping,
                    'count' => $count,
                ];
            }
        });

        return $results;
    }

    public function archiveYear(AcademicYear $year): void
    {
        $year->update(['is_active' => false, 'is_archived' => true]);
    }

    public function activateYear(AcademicYear $year): void
    {
        $year->update(['is_active' => true, 'is_archived' => false]);
    }
}
