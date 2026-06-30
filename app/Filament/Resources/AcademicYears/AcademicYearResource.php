<?php

namespace App\Filament\Resources\AcademicYears;

use App\Filament\Resources\AcademicYears\Pages\ManageAcademicYears;
use App\Models\AcademicYear;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class AcademicYearResource extends Resource
{
    protected static ?string $model = AcademicYear::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Tahun Ajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Tahun Ajaran';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama Tahun Ajaran')
                    ->required(),
                DatePicker::make('start_date')
                    ->label('Tanggal Mulai')
                    ->required(),
                DatePicker::make('end_date')
                    ->label('Tanggal Selesai')
                    ->required(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->required(),
                Toggle::make('is_archived')
                    ->label('Diarsipkan')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Tahun Ajaran')
                    ->searchable(),
                TextColumn::make('start_date')
                    ->label('Tanggal Mulai')
                    ->date()
                    ->sortable(),
                TextColumn::make('end_date')
                    ->label('Tanggal Selesai')
                    ->date()
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (AcademicYear $record): string => match ($record->getStatusAttribute()) {
                        'active' => 'success',
                        'inactive' => 'gray',
                        'archived' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (AcademicYear $record): string => match ($record->getStatusAttribute()) {
                        'active' => 'Aktif',
                        'inactive' => 'Tidak Aktif',
                        'archived' => 'Diarsipkan',
                        default => $record->getStatusAttribute(),
                    }),
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
                    ->before(function (DeleteAction $action, AcademicYear $record) {
                        if ($record->semesters()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Tahun ajaran ini masih memiliki semester terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->grades()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Tahun ajaran ini masih memiliki nilai terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->attendances()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Tahun ajaran ini masih memiliki presensi terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        } elseif ($record->classLearners()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Tahun ajaran ini masih memiliki penempatan kelas terkait.')
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
                                if ($record->semesters()->count() > 0 || $record->grades()->count() > 0 || $record->attendances()->count() > 0 || $record->classLearners()->count() > 0) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Beberapa tahun ajaran masih memiliki data terkait.')
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
            'index' => ManageAcademicYears::route('/'),
        ];
    }
}
