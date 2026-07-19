<?php

namespace App\Filament\Resources\Grades\Pages;

use App\Filament\Resources\Grades\GradeResource;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\HomeroomTeacher;
use App\Models\Semester;
use App\Models\Subject;
use App\Services\ExcelService;
use App\Services\GradeService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageGrades extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = GradeResource::class;

    protected static ?string $title = 'Dashboard Nilai';

    protected string $view = 'filament.resources.grades.pages.manage-grades';

    public ?int $class_id = null;

    public ?int $academic_year_id = null;

    public ?int $semester_id = null;

    public string $activeTab = 'input';

    public Collection $pivotSubjects;

    public int $totalStudents = 0;

    public int $totalGrades = 0;

    public int $publishedGrades = 0;

    public int $lockedGrades = 0;

    public function getBreadcrumbs(): array
    {
        return [
            'Akademik' => null,
            'Nilai' => GradeResource::getUrl('index'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->visible(fn () => $this->activeTab === 'input')
                ->form(fn (Schema $schema): Schema => $this->gradeFormSchema($schema)),
            Action::make('import')
                ->label('Import Nilai')
                ->icon('heroicon-o-arrow-up-tray')
                ->url(fn (): string => route('filament.admin.pages.import-grade'))
                ->color('primary')
                ->visible(fn () => $this->activeTab === 'input'),
            Action::make('export')
                ->label('Ekspor Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('exportPivot')
                ->visible(fn () => $this->activeTab === 'pivot'),
            Action::make('manageInput')
                ->label('Input Nilai')
                ->icon('heroicon-o-pencil-square')
                ->action(fn () => $this->setActiveTab('input'))
                ->color('gray')
                ->visible(fn () => $this->activeTab === 'pivot'),
            Action::make('managePivot')
                ->label('Rekap Nilai')
                ->icon('heroicon-o-table-cells')
                ->action(fn () => $this->setActiveTab('pivot'))
                ->color('gray')
                ->visible(fn () => $this->activeTab === 'input'),
        ];
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetTable();
    }

    protected function gradeFormSchema(Schema $schema): Schema
    {
        return GradeResource::form($schema);
    }

    public function mount(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->where('is_archived', false)->first();
        $this->academic_year_id = $activeYear?->id;
        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;
        $this->semester_id = $activeSemester?->id;

        $this->pivotSubjects = Subject::orderBy('name')->get();

        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        if ($homeroomClassIds->isNotEmpty()) {
            $this->class_id = $homeroomClassIds->count() === 1 ? $homeroomClassIds->first() : null;
        } elseif ($user->hasRole('admin')) {
            $this->class_id = null;
        }

        $this->refreshStats();
    }

    public function updatedClassId(): void
    {
        $this->academic_year_id = null;
        $this->semester_id = null;
        $this->resetTable();
        $this->refreshStats();
    }

    public function updatedAcademicYearId(): void
    {
        $this->semester_id = null;
        $this->resetTable();
        $this->refreshStats();
    }

    public function updatedSemesterId(): void
    {
        $this->resetTable();
        $this->refreshStats();
    }

    public function refreshStats(): void
    {
        $classIds = $this->getAccessibleClassIds();

        if (empty($classIds)) {
            $this->totalStudents = 0;
            $this->totalGrades = 0;
            $this->publishedGrades = 0;
            $this->lockedGrades = 0;

            return;
        }

        $learnerCountQuery = ClassLearner::whereIn('class_id', $classIds)
            ->when($this->academic_year_id, fn ($q) => $q->where('academic_year_id', $this->academic_year_id));

        $this->totalStudents = $learnerCountQuery->distinct('learner_id')->count('learner_id');

        $gradeQuery = Grade::whereIn('learner_id', function ($q) use ($classIds) {
            $q->select('learner_id')->from('class_learners')->whereIn('class_id', $classIds);
        })
            ->when($this->academic_year_id, fn ($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->when($this->semester_id, fn ($q) => $q->where('semester_id', $this->semester_id));

        $this->totalGrades = (clone $gradeQuery)->count();
        $this->publishedGrades = (clone $gradeQuery)->where('status', 'published')->count();
        $this->lockedGrades = (clone $gradeQuery)->where('status', 'locked')->count();
    }

    protected function getAccessibleClassIds(): array
    {
        if ($this->class_id) {
            return [$this->class_id];
        }

        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        if ($homeroomClassIds->isNotEmpty()) {
            return $homeroomClassIds->toArray();
        }

        if ($user->hasRole('admin')) {
            return Classes::pluck('id')->toArray();
        }

        return [];
    }

    public function filterForm(Schema $schema): Schema
    {
        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');
        $components = [];

        if ($homeroomClassIds->isEmpty() || $homeroomClassIds->count() > 1) {
            $classOptions = $homeroomClassIds->isNotEmpty()
                ? Classes::whereIn('id', $homeroomClassIds)->pluck('name', 'id')
                : Classes::pluck('name', 'id');

            $components[] = Select::make('class_id')
                ->label('Kelas')
                ->options($classOptions)
                ->placeholder('Semua Kelas')
                ->live()
                ->afterStateUpdated(fn () => $this->updatedClassId());
        }

        $components[] = Select::make('academic_year_id')
            ->label('Tahun Ajaran')
            ->options(fn () => AcademicYear::where('is_archived', false)->pluck('name', 'id'))
            ->placeholder('Semua Tahun Ajaran')
            ->live()
            ->afterStateUpdated(fn () => $this->updatedAcademicYearId());

        $components[] = Select::make('semester_id')
            ->label('Semester')
            ->options(fn () => $this->getSemesterOptions())
            ->placeholder('Semua Semester')
            ->live()
            ->afterStateUpdated(fn () => $this->updatedSemesterId());

        return $schema
            ->components($components);
    }

    protected function getSemesterOptions(): array
    {
        $query = Semester::whereHas('academicYear', fn ($q) => $q->where('is_archived', false));

        if ($this->academic_year_id) {
            $query->where('academic_year_id', $this->academic_year_id);
        }

        return $query->pluck('name', 'id')->toArray();
    }

    public function table(Table $table): Table
    {
        if ($this->activeTab === 'pivot') {
            return $this->buildPivotTable($table);
        }

        return $this->buildInputTable($table);
    }

    protected function buildInputTable(Table $table): Table
    {
        return $table
            ->query($this->getInputQuery())
            ->columns([
                TextColumn::make('learner.name')
                    ->label('Peserta Didik')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('subject.name')
                    ->label('Mata Pelajaran')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('task_score')
                    ->label('Tugas')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('pts_score')
                    ->label('PTS')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('pas_score')
                    ->label('PAS')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('practice_score')
                    ->label('Praktik')
                    ->numeric(2)
                    ->sortable(),
                TextColumn::make('final_score')
                    ->label('Nilai Akhir')
                    ->numeric(2)
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
                    }),
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
                    }),
            ])
            ->filters([
                SelectFilter::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->relationship('subject', 'name')
                    ->placeholder('Semua Mata Pelajaran'),
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Konsep',
                        'published' => 'Diterbitkan',
                        'locked' => 'Terkunci',
                    ])
                    ->placeholder('Semua Status'),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->recordActions([
                EditAction::make()
                    ->form(fn (Schema $schema): Schema => $this->gradeFormSchema($schema)),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
                BulkAction::make('publishSelected')
                    ->label('Terbitkan')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->action(function (Collection $records): void {
                        $records->each(fn (Grade $grade) => $grade->status === 'draft'
                            ? app(GradeService::class)->publishGrade($grade) : null);
                        Notification::make()->title('Nilai diterbitkan')->success()->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('learner_id');
    }

    protected function getInputQuery(): Builder
    {
        $query = Grade::query();

        $classIds = $this->getAccessibleClassIds();

        if (empty($classIds) && ! Filament::auth()->user()->hasRole('admin')) {
            return $query->whereRaw('0 = 1');
        }

        if (! empty($classIds)) {
            $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
            $query->whereIn('learner_id', $learnerIds);
        }

        return $query
            ->when($this->academic_year_id, fn ($q) => $q->where('academic_year_id', $this->academic_year_id))
            ->when($this->semester_id, fn ($q) => $q->where('semester_id', $this->semester_id));
    }

    protected function buildPivotTable(Table $table): Table
    {
        $subjects = Subject::orderBy('name')->get();
        $this->pivotSubjects = $subjects;

        $columns = [
            TextColumn::make('learner_name')
                ->label('Peserta Didik')
                ->searchable()
                ->sortable(),
            TextColumn::make('academic_year')
                ->label('Tahun Ajaran')
                ->sortable(),
            TextColumn::make('semester')
                ->label('Semester')
                ->sortable(),
        ];

        foreach ($subjects as $subject) {
            $subjectId = $subject->id;

            $columns[] = TextColumn::make("subject_{$subjectId}")
                ->label($subject->name)
                ->alignCenter()
                ->sortable()
                ->state(function (mixed $record) use ($subjectId): string {
                    return $record?->{"subject_{$subjectId}"} ?? '-';
                })
                ->color(function (string $state): ?string {
                    if ($state === '-' || $state === '') {
                        return null;
                    }

                    return (float) $state >= 75 ? 'success' : 'danger';
                });
        }

        return $table
            ->query($this->getPivotQuery())
            ->columns($columns)
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->defaultKeySort(false)
            ->defaultSort('learner_name');
    }

    protected function getPivotQuery(): Builder
    {
        $classIds = $this->getAccessibleClassIds();

        if (empty($classIds) && ! Filament::auth()->user()->hasRole('admin')) {
            return Grade::query()->whereRaw('0 = 1');
        }

        $subjectIds = Subject::orderBy('name')->pluck('id');
        $subjectCasts = $subjectIds->map(fn ($id) => "MAX(CASE WHEN g.subject_id = {$id} THEN g.final_score END) as subject_{$id}")->implode(', ');

        $sub = DB::table('grades', 'g')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw($subjectCasts)
            )
            ->join('learners as l', 'l.id', '=', 'g.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'g.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'g.semester_id')
            ->when($this->academic_year_id, fn ($q) => $q->where('g.academic_year_id', $this->academic_year_id))
            ->when($this->semester_id, fn ($q) => $q->where('g.semester_id', $this->semester_id))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('g.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'ay.id', 'ay.name', 's.id', 's.name');

        return Grade::query()
            ->fromSub($sub, 'grade_pivot')
            ->select('*');
    }

    public function getTableRecordKey(Model|array $record): string
    {
        if ($record instanceof Model) {
            return $record->getKey() ?? spl_object_id($record);
        }

        return parent::getTableRecordKey($record);
    }

    public function exportPivot(): StreamedResponse
    {
        $classIds = $this->getAccessibleClassIds();

        if (empty($classIds) && ! Filament::auth()->user()->hasRole('admin')) {
            abort(403);
        }

        $records = DB::table('grades', 'g')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw(Subject::orderBy('name')->pluck('id')->map(fn ($id) => "MAX(CASE WHEN g.subject_id = {$id} THEN g.final_score END) as subject_{$id}")->implode(', '))
            )
            ->join('learners as l', 'l.id', '=', 'g.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'g.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'g.semester_id')
            ->when($this->academic_year_id, fn ($q) => $q->where('g.academic_year_id', $this->academic_year_id))
            ->when($this->semester_id, fn ($q) => $q->where('g.semester_id', $this->semester_id))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('g.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'ay.id', 'ay.name', 's.id', 's.name')
            ->orderBy('l.name')
            ->get();

        $academicYearName = $this->academic_year_id ? AcademicYear::find($this->academic_year_id)?->name : 'all';
        $semesterName = $this->semester_id ? Semester::find($this->semester_id)?->name : 'all';
        $className = $this->class_id ? Classes::find($this->class_id)?->name : 'all';

        return app(ExcelService::class)->exportPivotGrades(
            $records,
            $this->pivotSubjects,
            $className,
            $academicYearName,
            $semesterName
        );
    }
}
