<?php

namespace App\Filament\Resources\HomeroomTeachers;

use App\Filament\Resources\HomeroomTeachers\Pages\ManageHomeroomTeachers;
use App\Models\HomeroomTeacher;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class HomeroomTeacherResource extends Resource
{
    protected static ?string $model = HomeroomTeacher::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'Wali Kelas';

    }

    public static function getPluralModelLabel(): string
    {
        return 'Wali Kelas';

    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('user_id')->label('Pengguna')->relationship('user',
            'name')->required(),
            Select::make('class_id')->label('Kelas')->relationship('classes',
                'name')->required(),
            Select::make('academic_year_id')->label('Tahun Ajaran')->relationship('academicYear',
                'name',
                fn ($query) => $query->where('is_archived',
                    false))->required(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table->recordTitleAttribute('id')->columns([TextColumn::make('user.name')->label('Pengguna')->searchable(),
            TextColumn::make('classes.name')->label('Kelas')->sortable(),
            TextColumn::make('academicYear.name')->label('Tahun Akademik')->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
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
        return ['index' => ManageHomeroomTeachers::route('/'),
        ];

    }
}
