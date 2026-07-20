<?php

namespace App\Filament\Resources\Learners\Pages;

use App\Filament\Resources\Learners\LearnerResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLearners extends ManageRecords
{
    protected static string $resource = LearnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Peserta Didik')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-learner'))
                ->color('primary'),
            CreateAction::make(),
        ];
    }
}
