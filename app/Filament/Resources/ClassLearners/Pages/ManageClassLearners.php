<?php

namespace App\Filament\Resources\ClassLearners\Pages;

use App\Filament\Resources\ClassLearners\ClassLearnerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageClassLearners extends ManageRecords
{
    protected static string $resource = ClassLearnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
