<?php

namespace App\Filament\Resources\Subjects;

use App\Filament\Imports\SubjectImporter;
use App\Filament\Resources\Subjects\Pages\ManageSubjects;
use App\Models\Subject;
use App\Models\SubjectGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ImportAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SubjectResource extends Resource
{
    protected static ?string $model = Subject::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static string|UnitEnum|null $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 7;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getModelLabel(): string
    {
        return 'Mata Pelajaran';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Mata Pelajaran';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->columns(2)
            ->components([
                Select::make('class_id')
                    ->label('Kelas')
                    ->relationship('classes', 'name')
                    ->required(),
                Select::make('subject_group_id')
                    ->label('Kelompok Mata Pelajaran')
                    ->options(fn () => SubjectGroup::where('is_active', true)->pluck('name', 'id'))
                    ->placeholder('Pilih Kelompok'),
                TextInput::make('code')
                    ->label('Kode')
                    ->required()
                    ->maxLength(255),
                TextInput::make('name')
                    ->label('Nama Mata Pelajaran')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->default(null)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('classes.name')
                    ->label('Kelas')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('subjectGroup.name')
                    ->label('Kelompok')
                    ->searchable(),
                TextColumn::make('code')
                    ->label('Kode')
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Mata Pelajaran')
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
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->before(function (DeleteAction $action, Subject $record) {
                        if ($record->grades()->count() > 0) {
                            Notification::make()
                                ->warning()
                                ->title('Tidak dapat menghapus')
                                ->body('Mata pelajaran ini masih memiliki nilai terkait.')
                                ->persistent()
                                ->send();
                            $action->halt();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ImportAction::make(SubjectImporter::class)
                        ->label('Import'),
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, $records) {
                            foreach ($records as $record) {
                                if ($record->grades()->count() > 0) {
                                    Notification::make()
                                        ->warning()
                                        ->title('Tidak dapat menghapus')
                                        ->body('Beberapa mata pelajaran masih memiliki nilai terkait.')
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
            'index' => ManageSubjects::route('/'),
        ];
    }
}
