<?php

namespace App\Filament\Resources\Grades;

use App\Filament\Resources\Grades\Pages\ManageGradePivot;
use App\Filament\Resources\Grades\Pages\ManageGrades;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\HomeroomTeacher;
use App\Models\Learner;
use App\Models\Semester;
use App\Services\GradeService;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class GradeResource extends Resource
{
    protected static ?string $model = Grade::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-list';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'id';

    public static function getModelLabel(): string
    {
        return 'Nilai';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Nilai';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('learner_id')
                    ->label('Peserta Didik')
                    ->options(function (): array {
                        $user = Filament::auth()->user();
                        $classIds = HomeroomTeacher::where('user_id', $user->id)
                            ->pluck('class_id');

                        if ($classIds->isNotEmpty()) {
                            $learnerIds = ClassLearner::whereIn('class_id', $classIds)
                                ->pluck('learner_id');

                            return Learner::whereIn('id', $learnerIds)
                                ->pluck('name', 'id')
                                ->toArray();
                        }

                        if ($user->hasRole('admin')) {
                            return Learner::pluck('name', 'id')->toArray();
                        }

                        return [];
                    })
                    ->searchable()
                    ->required(),
                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->required(),
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name', fn ($query) => $query->where('is_archived', false))
                    ->required(),
                Select::make('semester_id')
                    ->label('Semester')
                    ->relationship('semester', 'name', fn ($query) => $query->whereHas('academicYear', fn ($q) => $q->where('is_archived', false)))
                    ->required(),
                TextInput::make('task_score')
                    ->label('Tugas')
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateFinalScore($get, $set)),
                TextInput::make('pts_score')
                    ->label('Nilai PTS')
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateFinalScore($get, $set)),
                TextInput::make('pas_score')
                    ->label('Nilai PAS')
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateFinalScore($get, $set)),
                TextInput::make('practice_score')
                    ->label('Praktik')
                    ->numeric()
                    ->default(null)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (Get $get, Set $set) => static::updateFinalScore($get, $set)),
                TextInput::make('final_score')
                    ->label('Nilai Akhir')
                    ->numeric()
                    ->disabled()
                    ->dehydrated(),
                Select::make('predicate')
                    ->label('Predikat')
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                    ])
                    ->disabled()
                    ->dehydrated()
                    ->nullable(),
                Textarea::make('description')
                    ->label('Keterangan')
                    ->default(null)
                    ->columnSpanFull(),
                Textarea::make('competency_description')
                    ->label('Capaian Kompetensi')
                    ->default(null)
                    ->columnSpanFull(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Konsep',
                        'published' => 'Diterbitkan',
                        'locked' => 'Terkunci',
                    ])
                    ->required()
                    ->default('draft')
                    ->selectablePlaceholder(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->recordTitleAttribute('id')
            ->modifyQueryUsing(function (Builder $query) {
                $user = Filament::auth()->user();
                $classIds = HomeroomTeacher::where('user_id', $user->id)
                    ->pluck('class_id');

                if ($classIds->isNotEmpty()) {
                    $learnerIds = ClassLearner::whereIn('class_id', $classIds)
                        ->pluck('learner_id');

                    $query->whereIn('learner_id', $learnerIds);

                    return;
                }

                if (! $user->hasRole('admin')) {
                    $query->whereRaw('0 = 1');
                }
            })
            ->columns([
                TextColumn::make('learner.name')
                    ->label('Peserta Didik')
                    ->searchable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable(),
                TextColumn::make('academicYear.name')
                    ->label('Tahun Akademik')
                    ->searchable(),
                TextColumn::make('semester.name')
                    ->label('Semester')
                    ->searchable(),
                TextColumn::make('task_score')
                    ->label('Tugas')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pts_score')
                    ->label('PTS')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('pas_score')
                    ->label('PAS')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('practice_score')
                    ->label('Praktek')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label('Nilai Akhir')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('predicate')
                    ->label('Predikat')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'A' => 'success',
                        'B' => 'info',
                        'C' => 'warning',
                        'D' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'published' => 'success',
                        'locked' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Konsep',
                        'published' => 'Diterbitkan',
                        'locked' => 'Terkunci',
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
                SelectFilter::make('class_id')
                    ->label('Kelas')
                    ->options(function (): array {
                        $user = Filament::auth()->user();
                        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

                        if ($homeroomClassIds->isNotEmpty()) {
                            return Classes::whereIn('id', $homeroomClassIds)->pluck('name', 'id')->toArray();
                        }

                        if ($user->hasRole('admin')) {
                            return Classes::pluck('name', 'id')->toArray();
                        }

                        return [];
                    })
                    ->placeholder('Semua Kelas')
                    ->multiple()
                    ->query(function (Builder $query, array $data): Builder {
                        $classIds = $data['values'] ?? [];

                        if (empty($classIds)) {
                            return $query;
                        }

                        $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');

                        return $query->whereIn('learner_id', $learnerIds);
                    }),
                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->placeholder('Semua Mata Pelajaran')
                    ->multiple(),
                SelectFilter::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->relationship('academicYear', 'name', fn ($query) => $query->where('is_archived', false)->where('is_active', true))
                    ->placeholder('Semua Tahun Ajaran'),
                SelectFilter::make('semester_id')
                    ->label('Semester')
                    ->placeholder('Semua Semester')
                    ->options(function ($livewire): array {
                        $filters = $livewire->tableFilters;
                        $academicYearId = $filters['academic_year_id']['value'] ?? null;

                        $query = Semester::whereHas('academicYear', fn ($q) => $q->where('is_archived', false));

                        if ($academicYearId) {
                            $query->where('academic_year_id', $academicYearId);
                        }

                        return $query->pluck('name', 'id')->toArray();
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()
                    ->visible(fn () => auth()->check()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => auth()->check()),
                ]),
            ]);
    }

    public static function updateFinalScore(Get $get, Set $set): void
    {
        $task = (float) ($get('task_score') ?? 0);
        $pts = (float) ($get('pts_score') ?? 0);
        $pas = (float) ($get('pas_score') ?? 0);
        $practice = (float) ($get('practice_score') ?? 0);

        $service = app(GradeService::class);
        $finalScore = $service->calculateFinalScore($task, $pts, $pas, $practice);
        $predicate = $service->determinePredicate($finalScore);

        $set('final_score', $finalScore);
        $set('predicate', $predicate);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGrades::route('/'),
            'rekap' => ManageGradePivot::route('/rekap'),
        ];
    }
}
