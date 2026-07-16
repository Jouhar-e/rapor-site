<?php

namespace App\Filament\Resources\ClassLearners;

use App\Filament\Resources\ClassLearners\Pages\ManageClassLearners;
use App\Models\ClassLearner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class ClassLearnerResource extends Resource
{
    protected static ?string $model = ClassLearner::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'Penempatan Kelas';

    }

    public static function getPluralModelLabel(): string
    {
        return 'Penempatan Kelas';

    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('learner_id')->relationship('learner',
            'name')->required(),
            TextInput::make('class_id')->required()->numeric(),
            Select::make('academic_year_id')->label('Tahun Ajaran')->relationship('academicYear',
                'name',
                fn ($query) => $query->where('is_archived',
                    false))->required(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table->recordTitleAttribute('id')->columns([TextColumn::make('learner.name')->searchable(),
            TextColumn::make('class_id')->numeric()->sortable(),
            TextColumn::make('academicYear.name')->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            SelectFilter::make('academic_year_id')
                ->label('Tahun Ajaran')
                ->relationship('academicYear', 'name', fn ($query) => $query->where('is_archived', false))
                ->placeholder('Semua Tahun Ajaran'),
            SelectFilter::make('semester_id')
                ->label('Semester')
                ->relationship('semester', 'name', fn ($query) => $query->whereHas('academicYear', fn ($q) => $q->where('is_archived', false)))
                ->placeholder('Semua Semester'),
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
        return ['index' => ManageClassLearners::route('/'),
        ];

    }
}
