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
use Filament\Actions\Action;
use Filament\Facades\Filament;
use Filament\Forms\Components\Select;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageGradePivot extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = GradeResource::class;

    protected string $view = 'filament.resources.grades.pages.manage-grade-pivot';

    protected static ?string $title = 'Rekap Nilai';

    public ?int $class_id = null;

    public ?int $academic_year_id = null;

    public ?int $semester_id = null;

    public Collection $subjects;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage')
                ->label('Input Nilai')
                ->icon('heroicon-o-pencil-square')
                ->url(GradeResource::getUrl('manage')),
            Action::make('export')
                ->label('Ekspor Nilai')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportPivot')
                ->color('gray'),
        ];
    }

    public function exportPivot(): StreamedResponse
    {
        $user = Filament::auth()->user();

        $classIds = [];
        if ($this->class_id) {
            $classIds = [$this->class_id];
        } elseif ($user->hasRole('admin')) {
            $classIds = Classes::pluck('id')->toArray();
        } else {
            $classIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id')->toArray();
        }

        if (empty($classIds) && ! $user->hasRole('admin')) {
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
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('g.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('g.semester_id', $v))
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

        $excel = app(ExcelService::class);

        return $excel->exportPivotGrades(
            $records,
            $this->subjects,
            $className,
            $academicYearName,
            $semesterName
        );
    }

    public function mount(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->where('is_archived', false)->first();
        $this->academic_year_id = $activeYear?->id;
        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;
        $this->semester_id = $activeSemester?->id;

        $this->subjects = Subject::orderBy('name')->get();

        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        if ($homeroomClassIds->isNotEmpty()) {
            // Teachers with exactly 1 homeroom class → auto-select, no filter
            if ($homeroomClassIds->count() === 1) {
                $this->class_id = $homeroomClassIds->first();
            } else {
                // Teachers with multiple homeroom classes → show filter
                $this->class_id = null;
            }
        } elseif ($user->hasRole('admin')) {
            // Admins → show filter (can select any class)
            $this->class_id = null;
        }
    }

    public function filterForm(Schema $schema): Schema
    {
        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        $components = [];

        // Hide class filter for teachers with exactly 1 homeroom class
        // Show for admins or teachers with multiple homeroom classes
        if ($homeroomClassIds->isEmpty() || $homeroomClassIds->count() > 1) {
            $classOptions = $homeroomClassIds->isNotEmpty()
                ? Classes::whereIn('id', $homeroomClassIds)->pluck('name', 'id')->toArray()
                : Classes::pluck('name', 'id')->toArray();

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
            ->options(fn () => $this->academic_year_id
                ? Semester::where('academic_year_id', $this->academic_year_id)
                    ->whereHas('academicYear', fn ($q) => $q->where('is_archived', false))
                    ->pluck('name', 'id')
                : Semester::whereHas('academicYear', fn ($q) => $q->where('is_archived', false))
                    ->pluck('name', 'id')
            )
            ->placeholder('Semua Semester')
            ->live()
            ->afterStateUpdated(fn () => $this->updatedSemesterId());

        return $schema
            ->components($components);
    }

    public function table(Table $table): Table
    {
        $subjects = Subject::orderBy('name')->get();

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
            ->query($this->getTableQuery())
            ->columns($columns)
            ->paginated(false)
            ->defaultKeySort(false)
            ->defaultSort('learner_name');
    }

    protected function getTableQuery(): Builder
    {
        $user = Filament::auth()->user();

        $classIds = [];
        if ($this->class_id) {
            $classIds = [$this->class_id];
        } elseif ($user->hasRole('admin')) {
            $classIds = Classes::pluck('id')->toArray();
        } else {
            $classIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id')->toArray();
        }

        if (empty($classIds) && ! $user->hasRole('admin')) {
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
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('g.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('g.semester_id', $v))
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

    public function updatedAcademicYearId(): void
    {
        $this->semester_id = null;
        $this->resetTable();
    }

    public function updatedSemesterId(): void
    {
        $this->resetTable();
    }

    public function updatedClassId(): void
    {
        $this->academic_year_id = null;
        $this->semester_id = null;
        $this->resetTable();
    }
}
