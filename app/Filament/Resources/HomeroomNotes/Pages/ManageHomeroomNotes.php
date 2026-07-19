<?php

namespace App\Filament\Resources\HomeroomNotes\Pages;

use App\Filament\Resources\HomeroomNotes\HomeroomNoteResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageHomeroomNotes extends ManageRecords
{
    protected static string $resource = HomeroomNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Tabel Catatan')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => HomeroomNoteResource::getUrl('index'))
                ->color('gray'),
            Action::make('import')
                ->label('Import Catatan')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-homeroom-note'))
                ->color('primary'),
            CreateAction::make(),
        ];
    }
}
