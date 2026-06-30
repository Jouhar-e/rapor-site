<?php

namespace App\Filament\Resources\Semesters;

use App\Filament\Resources\Semesters\Pages\ManageSemesters;
use App\Models\Semester;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SemesterResource extends Resource
{
    protected static ?string $model = Semester::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Semester';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Semester';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name', fn ($query) => $query->where('is_archived', false))
                    ->required(),
                TextInput::make('name')
                    ->label('Nama Semester')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('academicYear.name')
                    ->label('Tahun Ajaran')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Semester')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
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
                    ->before(function (DeleteAction $action, Semester $record) {
                        if ($record->grades()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Semester ini masih memiliki nilai terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->attendances()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Semester ini masih memiliki presensi terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->learnerExtracurriculars()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Semester ini masih memiliki ekstrakurikuler terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->homeroomNotes()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Semester ini masih memiliki catatan wali kelas terkait.')
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
                                if ($record->grades()->count() > 0 || $record->attendances()->count() > 0 || $record->learnerExtracurriculars()->count() > 0 || $record->homeroomNotes()->count() > 0) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Beberapa semester masih memiliki data terkait.')
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
            'index' => ManageSemesters::route('/'),
        ];
    }
}
