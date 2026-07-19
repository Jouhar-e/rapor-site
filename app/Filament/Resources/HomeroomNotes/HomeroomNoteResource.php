<?php

namespace App\Filament\Resources\HomeroomNotes;

use App\Filament\Resources\HomeroomNotes\Pages\ManageHomeroomNotePivot;
use App\Filament\Resources\HomeroomNotes\Pages\ManageHomeroomNotes;
use App\Models\ClassLearner;
use App\Models\HomeroomNote;
use App\Models\HomeroomTeacher;
use App\Models\Learner;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class HomeroomNoteResource extends Resource
{
    protected static ?string $model = HomeroomNote::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-pencil-square';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 6;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'Catatan Wali Kelas';

    }

    public static function getPluralModelLabel(): string
    {
        return 'Catatan Wali Kelas';

    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([Select::make('learner_id')->label('Peserta Didik')->options(function (): array {
            $user = Filament::auth()->user();
            $classIds = HomeroomTeacher::where('user_id',
                $user->id)->pluck('class_id');
            if ($classIds->isNotEmpty()) {
                $learnerIds = ClassLearner::whereIn('class_id',
                    $classIds)->pluck('learner_id');

                return Learner::whereIn('id',
                    $learnerIds)->pluck('name',
                        'id')->toArray();

            }if ($user->hasRole('admin')) {
                return Learner::pluck('name',
                    'id')->toArray();

            }

            return [];

        })->searchable()->required(),
            Select::make('academic_year_id')->label('Tahun Ajaran')->relationship('academicYear',
                'name',
                fn ($query) => $query->where('is_archived',
                    false))->required(),
            Select::make('semester_id')->relationship('semester',
                'name')->required(),
            Textarea::make('note')->label('Catatan')->required()->columnSpanFull(),
        ]);

    }

    public static function table(Table $table): Table
    {
        return $table->recordTitleAttribute('id')->modifyQueryUsing(function (Builder $query) {
            $user = Filament::auth()->user();
            $classIds = HomeroomTeacher::where('user_id',
                $user->id)->pluck('class_id');
            if ($classIds->isNotEmpty()) {
                $learnerIds = ClassLearner::whereIn('class_id',
                    $classIds)->pluck('learner_id');
                $query->whereIn('learner_id',
                    $learnerIds);

                return;

            }if (! $user->hasRole('admin')) {
                $query->whereRaw('0 = 1');

            }
        })->columns([TextColumn::make('learner.name')->label('Peserta Didik')->searchable(),
            TextColumn::make('academicYear.name')->label('Tahun Ajaran')->searchable(),
            TextColumn::make('semester.name')->label('Semester')->searchable(),
            TextColumn::make('note')->label('Catatan')->limit(100)->searchable(),
            TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
            ->emptyStateHeading('Belum ada catatan')
            ->emptyStateDescription('Belum ada catatan wali kelas.')
            ->emptyStateIcon('heroicon-o-document-text')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);

    }

    public static function getPages(): array
    {
        return ['index' => ManageHomeroomNotePivot::route('/'),
            'manage' => ManageHomeroomNotes::route('/manage'),
        ];

    }
}
