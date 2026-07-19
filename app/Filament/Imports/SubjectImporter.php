<?php

namespace App\Filament\Imports;

use App\Models\Subject;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\ImportJob;

class SubjectImporter extends Importer
{
    protected static ?string $title = 'Mata Pelajaran';

    public static function getLabel(): string
    {
        return 'Mata Pelajaran';
    }

    public static function getSubtitle(): string
    {
        return 'Import data mata pelajaran dari file Excel';
    }

    public function getImportJob(): ImportJob
    {
        return new SubjectImportJob($this->data);
    }

    public function getColumns(): array
    {
        return [
            'class_name' => 'Kelas',
            'subject_group_name' => 'Kelompok Mata Pelajaran',
            'code' => 'Kode',
            'name' => 'Nama Mata Pelajaran',
            'description' => 'Keterangan',
            'is_active' => 'Aktif',
        ];
    }

    public function getRecord(): Subject
    {
        return new Subject;
    }
}
