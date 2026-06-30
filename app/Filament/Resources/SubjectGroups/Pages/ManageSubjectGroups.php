<?php

namespace App\Filament\Resources\SubjectGroups\Pages;

use App\Filament\Resources\SubjectGroups\SubjectGroupResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubjectGroups extends ManageRecords
{
    protected static string $resource = SubjectGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
