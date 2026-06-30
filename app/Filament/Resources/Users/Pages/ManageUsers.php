<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Spatie\Permission\Models\Role;

class ManageUsers extends ManageRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $roleIds = $data['roles'] ?? [];
                    unset($data['roles']);

                    $user = $this->getModel()::create($data);

                    if (! empty($roleIds)) {
                        $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
                        $user->assignRole($roleNames);
                    }

                    return $data;
                }),
        ];
    }
}
