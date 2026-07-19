<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shield-check';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 0;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Pengguna';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Pengguna';
    }

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->label('Kata Sandi')
                    ->password()
                    ->revealable()
                    ->required(fn (string $operation): bool => $operation === 'create')
                    ->minLength(8)
                    ->dehydrated(fn (?string $state): bool => filled($state))
                    ->dehydrateStateUsing(fn (string $state): string => Hash::make($state)),
                Select::make('roles')
                    ->label('Peran')
                    ->multiple()
                    ->options(fn () => Role::pluck('name', 'id'))
                    ->required()
                    ->default(fn () => Role::where('name', 'tutor')->value('id')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->searchable(),
                TextColumn::make('roles.name')
                    ->label('Peran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'admin' => 'danger',
                        'tutor' => 'success',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateRecordDataUsing(function (array $data, Model $record): array {
                        $data['roles'] = $record->roles->pluck('id')->toArray();

                        return $data;
                    })
                    ->using(function (Model $record, array $data): Model {
                        $roleIds = $data['roles'] ?? [];
                        unset($data['roles']);
                        $record->update($data);
                        if (! empty($roleIds)) {
                            $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
                            $record->syncRoles($roleNames);
                        }

                        return $record;
                    }),
                DeleteAction::make()
                    ->requiresConfirmation()
                    ->modalHeading('Hapus pengguna?')
                    ->modalDescription('Pengguna ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, hapus')
                    ->before(function (DeleteAction $action, User $record) {
                        $adminCount = User::role('admin')->count();
                        if ($record->hasRole('admin') && $adminCount <= 1) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Tidak dapat menghapus admin terakhir.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->emptyStateHeading('Belum ada pengguna')
            ->emptyStateDescription('Belum ada pengguna yang terdaftar di sistem. Silakan tambah pengguna baru.')
            ->emptyStateIcon('heroicon-o-users')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation()
                        ->modalHeading('Hapus pengguna terpilih?')
                        ->modalDescription('Pengguna terpilih akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.')
                        ->modalSubmitActionLabel('Ya, hapus')
                        ->before(function (DeleteBulkAction $action, $records) {
                            $adminCount = User::role('admin')->count();
                            foreach ($records as $record) {
                                if ($record->hasRole('admin') && $adminCount <= 1) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Tidak dapat menghapus admin terakhir.')
                                        ->persistent()
                                        ->send();
                                    $action->halt();

                                    return;
                                }
                            }
                        }),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageUsers::route('/'),
        ];
    }
}
