<?php

namespace App\Filament\Resources\LearnerExtracurriculars\Pages;

use App\Filament\Pages\ImportExtracurricular;
use App\Filament\Resources\LearnerExtracurriculars\LearnerExtracurricularResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageLearnerExtracurriculars extends ManageRecords
{
    protected static string $resource = LearnerExtracurricularResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Tabel Ekstrakurikuler')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => LearnerExtracurricularResource::getUrl('index'))
                ->color('gray'),
            Action::make('import')
                ->label('Import Nilai Ekstrakurikuler')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->url(fn (): string => ImportExtracurricular::getUrl()),
            CreateAction::make(),
        ];
    }
}
