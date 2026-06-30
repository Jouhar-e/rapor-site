<?php

namespace App\Filament\Resources\Phases\Pages;

use App\Filament\Resources\Phases\PhaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePhases extends ManageRecords
{
    protected static string $resource = PhaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
