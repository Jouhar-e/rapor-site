<?php

namespace App\Filament\Resources\Attendances\Pages;

use App\Filament\Resources\Attendances\AttendanceResource;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\HomeroomTeacher;
use App\Models\Semester;
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
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManageAttendancePivot extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = AttendanceResource::class;

    protected string $view = 'filament.resources.attendances.pages.manage-attendance-pivot';

    protected static ?string $title = 'Rekap Presensi';

    public ?int $academic_year_id = null;

    public ?int $semester_id = null;

    public function getBreadcrumbs(): array
    {
        return [
            AttendanceResource::getUrl('manage') => 'Presensi',
            'Rekap Presensi',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => AttendanceResource::getUrl('manage'))
                ->color('gray'),
            Action::make('manage')
                ->label('Input Presensi')
                ->icon('heroicon-o-pencil-square')
                ->url(AttendanceResource::getUrl('manage')),
            Action::make('export')
                ->label('Ekspor Presensi')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportPivot')
                ->color('success'),
        ];
    }

    public function exportPivot(): StreamedResponse
    {
        $user = Filament::auth()->user();

        $classIds = [];
        if ($user->hasRole('admin')) {
            $classIds = Classes::pluck('id')->toArray();
        } else {
            $classIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id')->toArray();
        }

        if (empty($classIds) && ! $user->hasRole('admin')) {
            abort(403);
        }

        $records = Attendance::query()
            ->select([
                'l.name as learner_name',
                'l.nis as nis',
                'ay.name as academic_year',
                's.name as semester',
                'a.sick',
                'a.permission',
                'a.absent',
            ])
            ->from('attendances as a')
            ->join('learners as l', 'l.id', '=', 'a.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'a.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'a.semester_id')
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('a.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('a.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('a.learner_id', $learnerIds);
            })
            ->orderBy('l.name')
            ->get();

        $academicYearName = $this->academic_year_id ? AcademicYear::find($this->academic_year_id)?->name : 'all';
        $semesterName = $this->semester_id ? Semester::find($this->semester_id)?->name : 'all';

        $excel = app(ExcelService::class);

        return $excel->exportAttendancePivot($records, $academicYearName, $semesterName);
    }

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
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(fn () => AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                    ->placeholder('Semua Tahun Ajaran')
                    ->live()
                    ->afterStateUpdated(fn () => $this->updatedAcademicYearId()),
                Select::make('semester_id')
                    ->label('Semester')
                    ->options(fn () => $this->academic_year_id
                        ? Semester::where('academic_year_id', $this->academic_year_id)->whereHas('academicYear', fn ($q) => $q->where('is_archived', false))->pluck('name', 'id')
                        : Semester::whereHas('academicYear', fn ($q) => $q->where('is_archived', false))->pluck('name', 'id')
                    )
                    ->placeholder('Semua Semester')
                    ->live()
                    ->afterStateUpdated(fn () => $this->updatedSemesterId()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->columns([
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
                TextColumn::make('sick')
                    ->label('Sakit')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('permission')
                    ->label('Izin')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('absent')
                    ->label('Tanpa Keterangan')
                    ->numeric()
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('total')
                    ->label('Total')
                    ->numeric()
                    ->sortable()
                    ->alignCenter()
                    ->state(function (mixed $record): string {
                        return (int) ($record?->sick ?? 0) + (int) ($record?->permission ?? 0) + (int) ($record?->absent ?? 0);
                    }),
            ])
            ->paginated(false)
            ->defaultKeySort(false)
            ->defaultSort('learner_name');
    }

    protected function getTableQuery(): Builder
    {
        $user = Filament::auth()->user();
        $classIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        if ($classIds->isEmpty() && ! $user->hasRole('admin')) {
            return Attendance::query()->whereRaw('0 = 1');
        }

        return Attendance::query()
            ->select([
                'l.name as learner_name',
                'ay.name as academic_year',
                's.name as semester',
                'a.sick',
                'a.permission',
                'a.absent',
            ])
            ->from('attendances as a')
            ->join('learners as l', 'l.id', '=', 'a.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'a.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'a.semester_id')
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('a.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('a.semester_id', $v))
            ->when($classIds->isNotEmpty(), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('a.learner_id', $learnerIds);
            });
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
}
