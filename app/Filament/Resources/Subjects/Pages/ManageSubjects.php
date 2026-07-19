<?php

namespace App\Filament\Resources\Subjects\Pages;

use App\Filament\Pages\ImportSubject;
use App\Filament\Resources\Subjects\SubjectResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubjects extends ManageRecords
{
    protected static string $resource = SubjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Mata Pelajaran')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('primary')
                ->url(fn (): string => ImportSubject::getUrl()),
            CreateAction::make(),
        ];
    }
}
