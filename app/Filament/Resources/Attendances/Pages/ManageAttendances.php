<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAttendances extends ManageRecords
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => AttendanceResource::getUrl('index'))
                ->color('gray'),
            Action::make('import')
                ->label('Import Presensi')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-attendance'))
                ->color('primary'),
            CreateAction::make(),
        ];
    }
}
