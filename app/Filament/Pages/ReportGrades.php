<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Grade;
use App\Models\Semester;
use App\Models\Subject;
use App\Services\ExcelService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ReportGrades extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $title = 'Laporan Nilai';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 3;

    protected ?string $heading = 'Laporan Nilai';

    protected string $view = 'filament.pages.report-grades';

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.dashboard') => 'Beranda',
            'Laporan',
            'Laporan Nilai',
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    public ?array $filters = [];

    public function mount(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->where('is_archived', false)->first();
        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;

        $this->filterForm->fill([
            'academic_year_id' => $activeYear?->id,
            'semester_id' => $activeSemester?->id,
        ]);
    }

    public function filterForm(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(Classes::pluck('name', 'id'))
                    ->placeholder('Semua Kelas')
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('academic_year_id', null);
                        $set('semester_id', null);
                        $this->resetTable();
                    }),
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicYear::where('is_archived', false)->pluck('name', 'id'))
                    ->placeholder('Semua Tahun Ajaran')
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('semester_id', null);
                        $this->resetTable();
                    }),
                Select::make('semester_id')
                    ->label('Semester')
                    ->options(fn (callable $get) => $get('academic_year_id')
                        ? Semester::where('academic_year_id', $get('academic_year_id'))
                            ->whereHas('academicYear', fn ($q) => $q->where('is_archived', false))
                            ->pluck('name', 'id')
                        : Semester::whereHas('academicYear', fn ($q) => $q->where('is_archived', false))
                            ->pluck('name', 'id')
                    )
                    ->placeholder('Semua Semester')
                    ->live()
                    ->afterStateUpdated(fn () => $this->resetTable()),
            ])
            ->statePath('filters');
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'filters.')) {
            $this->resetTable();
        }
    }

    public function table(Table $table): Table
    {
        $subjects = Subject::orderBy('name')->get();

        $columns = [
            TextColumn::make('learner_name')
                ->label('Peserta Didik')
                ->searchable()
                ->sortable(),
            TextColumn::make('class_name')
                ->label('Kelas')
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
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
            ->defaultKeySort(false)
            ->defaultSort('learner_name')
            ->headerActions([
                Action::make('exportExcel')
                    ->label('Ekspor Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => $this->exportExcel()),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $classIds = $this->filters['class_id'] ? [$this->filters['class_id']] : Classes::pluck('id')->toArray();

        $subjectIds = Subject::orderBy('name')->pluck('id');
        $subjectCasts = $subjectIds->map(fn ($id) => "MAX(CASE WHEN g.subject_id = {$id} THEN g.final_score END) as subject_{$id}")->implode(', ');

        $sub = DB::table('grades', 'g')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'c.name as class_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw($subjectCasts)
            )
            ->join('learners as l', 'l.id', '=', 'g.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'g.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'g.semester_id')
            ->leftJoin('class_learners as cl', function ($join) {
                $join->on('cl.learner_id', '=', 'g.learner_id')
                    ->whereColumn('cl.academic_year_id', '=', 'g.academic_year_id');
            })
            ->leftJoin('classes as c', 'c.id', '=', 'cl.class_id')
            ->when($this->filters['academic_year_id'] ?? null, fn ($q, $v) => $q->where('g.academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn ($q, $v) => $q->where('g.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('g.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'c.id', 'c.name', 'ay.id', 'ay.name', 's.id', 's.name');

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

    public function exportExcel(): StreamedResponse
    {
        $classIds = $this->filters['class_id'] ? [$this->filters['class_id']] : Classes::pluck('id')->toArray();

        $records = DB::table('grades', 'g')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'c.name as class_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw(Subject::orderBy('name')->pluck('id')->map(fn ($id) => "MAX(CASE WHEN g.subject_id = {$id} THEN g.final_score END) as subject_{$id}")->implode(', '))
            )
            ->join('learners as l', 'l.id', '=', 'g.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'g.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'g.semester_id')
            ->leftJoin('class_learners as cl', function ($join) {
                $join->on('cl.learner_id', '=', 'g.learner_id')
                    ->whereColumn('cl.academic_year_id', '=', 'g.academic_year_id');
            })
            ->leftJoin('classes as c', 'c.id', '=', 'cl.class_id')
            ->when($this->filters['academic_year_id'] ?? null, fn ($q, $v) => $q->where('g.academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn ($q, $v) => $q->where('g.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('g.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'c.id', 'c.name', 'ay.id', 'ay.name', 's.id', 's.name')
            ->orderBy('l.name')
            ->get();

        $subjects = Subject::orderBy('name')->get();
        $academicYearName = $this->filters['academic_year_id'] ?? null
            ? AcademicYear::find($this->filters['academic_year_id'])?->name
            : 'all';
        $semesterName = $this->filters['semester_id'] ?? null
            ? Semester::find($this->filters['semester_id'])?->name
            : 'all';
        $className = $this->filters['class_id'] ?? null
            ? Classes::find($this->filters['class_id'])?->name
            : 'all';

        return app(ExcelService::class)->exportPivotGrades(
            $records,
            $subjects,
            $className,
            $academicYearName,
            $semesterName,
        );
    }
}
