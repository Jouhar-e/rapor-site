<?php

namespace App\Filament\Resources\PromotionMappings\Pages;

use App\Filament\Resources\PromotionMappings\PromotionMappingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePromotionMappings extends ManageRecords
{
    protected static string $resource = PromotionMappingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
