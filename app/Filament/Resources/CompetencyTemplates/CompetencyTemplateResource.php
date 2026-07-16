<?php

namespace App\Filament\Resources\CompetencyTemplates;

use App\Filament\Resources\CompetencyTemplates\Pages\ManageCompetencyTemplates;
use App\Models\CompetencyTemplate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CompetencyTemplateResource extends Resource
{
    protected static ?string $model = CompetencyTemplate::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 4;

    protected static ?string $recordTitleAttribute = 'id';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('subject_id')
                ->label('Mata Pelajaran')
                ->relationship('subject', 'name')
                ->searchable()
                ->required(),
            Select::make('predicate')
                ->label('Predikat')
                ->options([
                    'A' => 'A',
                    'B' => 'B',
                    'C' => 'C',
                    'D' => 'D',
                ])
                ->required(),
            Textarea::make('achievement_text')
                ->label('Teks Capaian')
                ->helperText('Gunakan {nama} untuk menyisipkan nama peserta didik.')
                ->required()
                ->columnSpanFull(),
            Textarea::make('improvement_text')
                ->label('Teks Perbaikan')
                ->helperText('Gunakan {nama} untuk menyisipkan nama peserta didik.')
                ->required()
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                TextColumn::make('subject.name')->label('Mata Pelajaran')->searchable()->sortable(),
                TextColumn::make('predicate')->label('Predikat')->badge()->searchable(),
                TextColumn::make('achievement_text')->label('Teks Capaian')->limit(60),
                TextColumn::make('improvement_text')->label('Teks Perbaikan')->limit(60),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageCompetencyTemplates::route('/'),
        ];
    }
}
