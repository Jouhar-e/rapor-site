<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Learner;
use App\Models\Semester;
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

class ReportAttendances extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $title = 'Laporan Presensi';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 4;

    protected ?string $heading = 'Laporan Presensi';

    protected string $view = 'filament.pages.report-attendances';

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.dashboard') => 'Beranda',
            'Laporan',
            'Laporan Presensi',
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

        $this->form->fill([
            'academic_year_id' => $activeYear?->id,
            'semester_id' => $activeSemester?->id,
        ]);
    }

    public function form(Schema $schema): Schema
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
        return $table
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('nis')
                    ->label('NIS')
                    ->searchable(),
                TextColumn::make('learner_name')
                    ->label('Nama')
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
                TextColumn::make('total_sick')
                    ->label('Sakit')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('total_permission')
                    ->label('Izin')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('total_absent')
                    ->label('Tanpa Keterangan')
                    ->sortable()
                    ->alignCenter(),
            ])
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

        $sub = DB::table('attendances', 'a')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'c.name as class_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw('SUM(a.sick) as total_sick'),
                DB::raw('SUM(a.permission) as total_permission'),
                DB::raw('SUM(a.absent) as total_absent'),
            )
            ->join('learners as l', 'l.id', '=', 'a.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'a.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'a.semester_id')
            ->leftJoin('class_learners as cl', function ($join) {
                $join->on('cl.learner_id', '=', 'a.learner_id')
                    ->whereColumn('cl.academic_year_id', '=', 'a.academic_year_id');
            })
            ->leftJoin('classes as c', 'c.id', '=', 'cl.class_id')
            ->when($this->filters['academic_year_id'] ?? null, fn ($q, $v) => $q->where('a.academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn ($q, $v) => $q->where('a.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('a.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'c.id', 'c.name', 'ay.id', 'ay.name', 's.id', 's.name');

        return Learner::query()
            ->fromSub($sub, 'attendance_pivot')
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

        $rows = DB::table('attendances', 'a')
            ->select(
                'l.nis as nis',
                'l.name as learner_name',
                'c.name as class_name',
                'ay.name as academic_year',
                's.name as semester',
                DB::raw('SUM(a.sick) as total_sick'),
                DB::raw('SUM(a.permission) as total_permission'),
                DB::raw('SUM(a.absent) as total_absent'),
            )
            ->join('learners as l', 'l.id', '=', 'a.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'a.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'a.semester_id')
            ->leftJoin('class_learners as cl', function ($join) {
                $join->on('cl.learner_id', '=', 'a.learner_id')
                    ->whereColumn('cl.academic_year_id', '=', 'a.academic_year_id');
            })
            ->leftJoin('classes as c', 'c.id', '=', 'cl.class_id')
            ->when($this->filters['academic_year_id'] ?? null, fn ($q, $v) => $q->where('a.academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn ($q, $v) => $q->where('a.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('a.learner_id', $learnerIds);
            })
            ->groupBy('l.id', 'l.nis', 'l.name', 'c.id', 'c.name', 'ay.id', 'ay.name', 's.id', 's.name')
            ->orderBy('l.name')
            ->get();

        return app(ExcelService::class)->exportReport('attendances.xlsx', [
            'NIS', 'Nama', 'Kelas', 'Tahun Ajaran', 'Semester', 'Sakit', 'Izin', 'Tanpa Keterangan',
        ], $rows, fn ($row) => [
            $row->nis,
            $row->learner_name,
            $row->class_name,
            $row->academic_year,
            $row->semester,
            (int) $row->total_sick,
            (int) $row->total_permission,
            (int) $row->total_absent,
        ]);
    }
}
