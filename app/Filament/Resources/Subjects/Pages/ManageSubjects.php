<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Imports\SubjectImporter;
use App\Filament\Resources\Subjects\SubjectResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubjects extends ManageRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ImportAction::make(SubjectImporter::class)
                ->label('Import Mata Pelajaran')
                ->icon('heroicon-o-arrow-up-tray'),
            CreateAction::make(),
        ];
    }
}
