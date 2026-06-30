<?php

namespace App\Filament\Resources\LearnerExtracurriculars;

use App\Filament\Resources\LearnerExtracurriculars\Pages\ManageLearnerExtracurricularPivot;
use App\Filament\Resources\LearnerExtracurriculars\Pages\ManageLearnerExtracurriculars;
use App\Models\ClassLearner;
use App\Models\HomeroomTeacher;
use App\Models\LearnerExtracurricular;
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
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class LearnerExtracurricularResource extends Resource
{
    protected static ?string $model = LearnerExtracurricular::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 5;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'Nilai Ekstrakurikuler';

    }

    public static function getPluralModelLabel(): string
    {
        return 'Nilai Ekstrakurikuler';

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
                returnLearner::whereIn('id',
                    $learnerIds)->pluck('name',
                        'id')->toArray();

            }if ($user->hasRole('admin')) {
                returnLearner::pluck('name',
                    'id')->toArray();

            }

            return [];

        })->searchable()->required(),
            Select::make('extracurricular_id')->relationship('extracurricular',
                'name')->required(),
            Select::make('academic_year_id')->label('Tahun Ajaran')->relationship('academicYear',
                'name',
                fn ($query) => $query->where('is_archived',
                    false))->required(),
            Select::make('semester_id')->relationship('semester',
                'name')->required(),
            Select::make('predicate')->label('Predikat')->options(['A' => 'A (Sangat Baik)',
                'B' => 'B (Baik)',
                'C' => 'C (Cukup)',
                'D' => 'D (Kurang)',
            ])->placeholder('Pilih Predikat'),
            Textarea::make('description')->label('Keterangan')->default(null)->columnSpanFull(),
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
            TextColumn::make('extracurricular.name')->label('Ekstrakurikuler')->searchable(),
            TextColumn::make('academicYear.name')->label('Tahun Ajaran')->searchable(),
            TextColumn::make('semester.name')->label('Semester')->searchable(),
            TextColumn::make('predicate')->label('Predikat')->searchable(),
            TextColumn::make('created_at')->label('Dibuat')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('updated_at')->label('Diperbarui')->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
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
        return ['index' => ManageLearnerExtracurricularPivot::route('/'),
            'manage' => ManageLearnerExtracurriculars::route('/manage'),
        ];

    }
}
