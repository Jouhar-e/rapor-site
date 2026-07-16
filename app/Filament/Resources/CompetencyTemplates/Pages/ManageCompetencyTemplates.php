<?php

namespace App\Filament\Resources\CompetencyTemplates\Pages;

use App\Filament\Resources\CompetencyTemplates\CompetencyTemplateResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCompetencyTemplates extends ManageRecords
{
    protected static string $resource = CompetencyTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
