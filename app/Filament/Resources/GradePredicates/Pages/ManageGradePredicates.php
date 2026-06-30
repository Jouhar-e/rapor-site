<?php

namespace App\Filament\Resources\GradePredicates\Pages;

use App\Filament\Resources\GradePredicates\GradePredicateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageGradePredicates extends ManageRecords
{
    protected static string $resource = GradePredicateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
