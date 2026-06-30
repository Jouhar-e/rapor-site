<?php

namespace App\Services;

use App\Models\CompetencyTemplate;
use App\Models\Grade;

class CompetencyService
{
    public function generateDescription(Grade $grade): ?string
    {
        $template = CompetencyTemplate::where('subject_id', $grade->subject_id)
            ->where('predicate', $grade->predicate)
            ->first();

        if (! $template) {
            return null;
        }

        $learner = $grade->learner;

        $achievement = str_replace('{nama}', $learner->name, $template->achievement_text);

        $parts = [$achievement];

        if ($template->improvement_text) {
            $improvement = str_replace('{nama}', $learner->name, $template->improvement_text);
            $parts[] = $improvement;
        }

        return implode("\n\n", $parts);
    }

    public function generateAndSave(Grade $grade): Grade
    {
        $description = $this->generateDescription($grade);

        if ($description) {
            $grade->competency_description = $description;
            $grade->save();
        }

        return $grade->fresh();
    }
}
