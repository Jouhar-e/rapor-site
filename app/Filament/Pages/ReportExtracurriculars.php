<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Extracurricular;
use App\Models\Learner;
use App\Models\Semester;
use App\Services\ExcelService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
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

class ReportExtracurriculars extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $title = 'Laporan Ekstrakurikuler';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 5;

    protected ?string $heading = 'Laporan Ekstrakurikuler';

    protected string $view = 'filament.pages.report-extracurriculars';

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    public ?int $class_id = null;

    public ?int $academic_year_id = null;

    public ?int $semester_id = null;

    public function mount(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->where('is_archived', false)->first();
        $this->academic_year_id = $activeYear?->id;

        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;
        $this->semester_id = $activeSemester?->id;
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
                    ->afterStateUpdated(fn () => $this->updatedClassId()),
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicYear::where('is_archived', false)->pluck('name', 'id'))
                    ->placeholder('Semua Tahun Ajaran')
                    ->live()
                    ->afterStateUpdated(fn () => $this->updatedAcademicYearId()),
                Select::make('semester_id')
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
                    ->afterStateUpdated(fn () => $this->updatedSemesterId()),
            ]);
    }

    public function table(Table $table): Table
    {
        $extracurriculars = Extracurricular::orderBy('name')->get();

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

        foreach ($extracurriculars as $extracurricular) {
            $id = $extracurricular->id;

            $columns[] = TextColumn::make("extracurricular_{$id}")
                ->label($extracurricular->name)
                ->alignCenter()
                ->state(function (mixed $record) use ($id): string {
                    return $record?->{"extracurricular_{$id}"} ?? '-';
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
                    ->action(fn () => $this->exportExcel()),
            ]);
    }

    protected function getTableQuery(): Builder
    {
        $classIds = $this->class_id ? [$this->class_id] : Classes::pluck('id')->toArray();

        $extracurricularIds = Extracurricular::orderBy('name')->pluck('id');
        $subjectCasts = $extracurricularIds->map(fn ($id) => "MAX(CASE WHEN le.extracurricular_id = {$id} THEN le.predicate END) as extracurricular_{$id}")->implode(', ');

        $sub = DB::table('learner_extracurriculars', 'le')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'c.name as class_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw($subjectCasts)
            )
            ->join('learners as l', 'l.id', '=', 'le.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'le.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'le.semester_id')
            ->leftJoin('class_learners as cl', function ($join) {
                $join->on('cl.learner_id', '=', 'le.learner_id')
                    ->whereColumn('cl.academic_year_id', '=', 'le.academic_year_id');
            })
            ->leftJoin('classes as c', 'c.id', '=', 'cl.class_id')
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('le.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('le.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('le.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'c.id', 'c.name', 'ay.id', 'ay.name', 's.id', 's.name');

        return Learner::query()
            ->fromSub($sub, 'extracurricular_pivot')
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
        $classIds = $this->class_id ? [$this->class_id] : Classes::pluck('id')->toArray();
        $extracurricularIds = Extracurricular::orderBy('name')->pluck('id');

        $records = DB::table('learner_extracurriculars', 'le')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'c.name as class_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw($extracurricularIds->map(fn ($id) => "MAX(CASE WHEN le.extracurricular_id = {$id} THEN le.predicate END) as extracurricular_{$id}")->implode(', '))
            )
            ->join('learners as l', 'l.id', '=', 'le.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'le.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'le.semester_id')
            ->leftJoin('class_learners as cl', function ($join) {
                $join->on('cl.learner_id', '=', 'le.learner_id')
                    ->whereColumn('cl.academic_year_id', '=', 'le.academic_year_id');
            })
            ->leftJoin('classes as c', 'c.id', '=', 'cl.class_id')
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('le.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('le.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('le.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'c.id', 'c.name', 'ay.id', 'ay.name', 's.id', 's.name')
            ->orderBy('l.name')
            ->get();

        $extracurriculars = Extracurricular::orderBy('name')->get();
        $academicYearName = $this->academic_year_id ? AcademicYear::find($this->academic_year_id)?->name : 'all';
        $semesterName = $this->semester_id ? Semester::find($this->semester_id)?->name : 'all';
        $className = $this->class_id ? Classes::find($this->class_id)?->name : 'all';

        return app(ExcelService::class)->exportExtracurricularPivot(
            $records,
            $academicYearName,
            $semesterName,
            $extracurriculars,
        );
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
