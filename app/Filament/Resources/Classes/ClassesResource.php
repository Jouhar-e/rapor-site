<?php

namespace App\Filament\Resources\Classes;

use App\Filament\Resources\Classes\Pages\ManageClasses;
use App\Models\Classes;
use App\Models\Phase;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ClassesResource extends Resource
{
    protected static ?string $model = Classes::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-building-library';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Kelas';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Kelas';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('program_id')
                    ->label('Program')
                    ->relationship('program', 'name')
                    ->required(),
                Select::make('phase_id')
                    ->label('Fase')
                    ->options(fn () => Phase::where('is_active', true)->pluck('name', 'id'))
                    ->placeholder('Pilih Fase'),
                TextInput::make('name')
                    ->label('Nama Kelas')
                    ->required()
                    ->maxLength(255),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'lulus' => 'Lulus',
                    ])
                    ->required()
                    ->default('aktif'),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('program.name')
                    ->label('Program')
                    ->searchable(),
                TextColumn::make('phase.name')
                    ->label('Fase')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Kelas')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'success',
                        'nonaktif' => 'danger',
                        'lulus' => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Nonaktif',
                        'lulus' => 'Lulus',
                        default => $state,
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
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Classes $record) {
                        if ($record->classLearners()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki peserta didik terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->subjects()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki mata pelajaran terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->homeroomTeachers()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki wali kelas terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->sourcePromotionMappings()->count() > 0 || $record->destinationPromotionMappings()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Kelas ini masih memiliki pemetaan kenaikan kelas terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records) {
                            foreach ($records as $record) {
                                if ($record->classLearners()->count() > 0 || $record->subjects()->count() > 0 || $record->homeroomTeachers()->count() > 0 || $record->sourcePromotionMappings()->count() > 0 || $record->destinationPromotionMappings()->count() > 0) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Beberapa kelas masih memiliki data terkait.')
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
            'index' => ManageClasses::route('/'),
        ];
    }
}
