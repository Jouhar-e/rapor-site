<?php

namespace App\Filament\Imports;

use App\Models\Classes;
use App\Models\Subject;
use App\Models\SubjectGroup;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class SubjectImporter extends Importer
{
    protected static ?string $model = Subject::class;

    public static function getLabel(): string
    {
        return 'Mata Pelajaran';
    }

    public static function getSubtitle(): string
    {
        return 'Import data mata pelajaran dari file Excel';
    }

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('class_name', 'Kelas')
                ->required()
                ->isMappingRequiredForNewRecordsOnly(),
            ImportColumn::make('subject_group_name', 'Kelompok Mata Pelajaran')
                ->required(),
            ImportColumn::make('code', 'Kode')
                ->required()
                ->numeric(false),
            ImportColumn::make('name', 'Nama Mata Pelajaran')
                ->required(),
            ImportColumn::make('description', 'Keterangan')
                ->numeric(false),
            ImportColumn::make('is_active', 'Aktif')
                ->boolean(),
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        return "Diimpor {$import->records_count} data.";
    }

    public function beforeSave(): void
    {
        // Resolve class_id from class_name column
        // Maps the human-readable class name from Excel to the actual class ID
        $className = $this->data['class_name'] ?? null;
        $class = Classes::where('name', $className)->first();

        if (! $class) {
            $this->record->is_not_importable = true;
            $this->record->import_error = "Kelas '{$className}' tidak ditemukan.";

            return;
        }

        // Resolve subject_group_id from subject_group_name column
        // Maps the human-readable subject group name from Excel to the actual subject group ID
        $subjectGroupName = $this->data['subject_group_name'] ?? null;
        $subjectGroup = SubjectGroup::where('name', $subjectGroupName)->first();

        if (! $subjectGroup) {
            $this->record->is_not_importable = true;
            $this->record->import_error = "Kelompok mata pelajaran '{$subjectGroupName}' tidak ditemukan.";

            return;
        }

        // Set the resolved IDs on the record before saving
        $this->record->class_id = $class->id;
        $this->record->subject_group_id = $subjectGroup->id;
    }
}
