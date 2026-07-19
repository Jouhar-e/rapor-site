<?php

namespace App\Filament\Resources\GradePredicates;

use App\Filament\Resources\GradePredicates\Pages\ManageGradePredicates;
use App\Models\GradePredicate;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class GradePredicateResource extends Resource
{
    protected static ?string $model = GradePredicate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'predicate';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');

    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([TextInput::make('min_score')->label('Nilai Minimal')->required()->numeric(),
            TextInput::make('max_score')->label('Nilai Maksimal')->required()->numeric(),
            TextInput::make('predicate')->label('Predikat')->required(),
            Textarea::make('description')->label('Keterangan')->default(null)->columnSpanFull(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table->recordTitleAttribute('predicate')->columns([TextColumn::make('min_score')->label('Nilai Minimal')->numeric()->sortable(),
            TextColumn::make('max_score')->label('Nilai Maksimal')->numeric()->sortable(),
            TextColumn::make('predicate')->label('Predikat')->searchable(),
            TextColumn::make('created_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
        ])->filters([
            //
        ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada predikat')
            ->emptyStateDescription('Belum ada predikat nilai yang dikonfigurasi.')
            ->emptyStateIcon('heroicon-o-document-chart-bar')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }

    public static function getPages(): array
    {
        return ['index' => ManageGradePredicates::route('/'),
        ];

    }
}
