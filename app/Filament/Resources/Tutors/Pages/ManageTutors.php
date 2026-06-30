<?php

namespace App\Filament\Resources\Tutors\Pages;

use App\Filament\Resources\Tutors\TutorResource;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Facades\Hash;

class ManageTutors extends ManageRecords
{
    protected static string $resource = TutorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('import')
                ->label('Import Tutor')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-tutor'))
                ->color('gray'),
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $user = User::create([
                        'name' => $data['name'],
                        'email' => $data['user_email'],
                        'password' => Hash::make($data['user_password']),
                    ]);
                    $user->assignRole('tutor');

                    $data['user_id'] = $user->id;
                    unset($data['user_email'], $data['user_password']);

                    return $data;
                }),
        ];
    }
}
