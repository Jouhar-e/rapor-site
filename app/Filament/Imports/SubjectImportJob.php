<?php

namespace App\Filament\Imports;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\SubjectGroup;
use Filament\Actions\Imports\ImportJob;

class SubjectImportJob extends ImportJob
{
    public function handle(): ?bool
    {
        $className = $this->data['class_name'] ?? null;
        $subjectGroupName = $this->data['subject_group_name'] ?? null;

        // Resolve class_id from class_name
        // Maps the human-readable class name from Excel to the actual class ID
        $class = Classes::where('name', $className)->first();
        if (! $class) {
            $this->record->is_not_importable = true;
            $this->record->import_error = "Kelas '{$className}' tidak ditemukan.";

            return false;
        }

        // Resolve subject_group_id from subject_group_name
        // Maps the human-readable subject group name from Excel to the actual subject group ID
        $subjectGroup = SubjectGroup::where('name', $subjectGroupName)->first();
        if (! $subjectGroup) {
            $this->record->is_not_importable = true;
            $this->record->import_error = "Kelompok mata pelajaran '{$subjectGroupName}' tidak ditemukan.";

            return false;
        }

        // Set resolved IDs on the record before saving
        $this->record->class_id = $class->id;
        $this->record->subject_group_id = $subjectGroup->id;

        return true;
    }

    public function save(): void
    {
        $this->record->save();
    }
}
