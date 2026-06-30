<?php

namespace App\Filament\Resources\BackupHistories\Pages;

use App\Filament\Resources\BackupHistories\BackupHistoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageBackupHistories extends ManageRecords
{
    protected static string $resource = BackupHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
