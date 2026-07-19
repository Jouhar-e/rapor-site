<?php

namespace App\Filament\Resources\HomeroomNotes\Pages;

use App\Filament\Resources\HomeroomNotes\HomeroomNoteResource;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\HomeroomNote;
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

class ManageHomeroomNotePivot extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = HomeroomNoteResource::class;

    protected string $view = 'filament.resources.homeroom-notes.pages.manage-homeroom-note-pivot';

    protected static ?string $title = 'Rekap Catatan Wali Kelas';

    public ?int $class_id = null;

    public ?int $academic_year_id = null;

    public ?int $semester_id = null;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('manage')
                ->label('Input Catatan')
                ->icon('heroicon-o-pencil-square')
                ->url(HomeroomNoteResource::getUrl('manage')),
            Action::make('export')
                ->label('Ekspor Catatan')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('exportPivot')
                ->color('success'),
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

        $records = HomeroomNote::query()
            ->select([
                'l.name as learner_name',
                'l.nis as nis',
                'ay.name as academic_year',
                's.name as semester',
                'hn.note',
            ])
            ->from('homeroom_notes as hn')
            ->join('learners as l', 'l.id', '=', 'hn.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'hn.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'hn.semester_id')
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('hn.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('hn.semester_id', $v))
            ->where('ay.is_archived', false)
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('hn.learner_id', $learnerIds);
            })
            ->orderBy('l.name')
            ->get();

        $academicYearName = $this->academic_year_id ? AcademicYear::find($this->academic_year_id)?->name : 'all';
        $semesterName = $this->semester_id ? Semester::find($this->semester_id)?->name : 'all';

        $excel = app(ExcelService::class);

        return $excel->exportHomeroomNotePivot($records, $academicYearName, $semesterName);
    }

    public function mount(): void
    {
        $activeYear = AcademicYear::where('is_active', true)->where('is_archived', false)->first();
        $this->academic_year_id = $activeYear?->id;
        $activeSemester = $activeYear
            ? Semester::where('academic_year_id', $activeYear->id)->where('is_active', true)->first()
            : null;
        $this->semester_id = $activeSemester?->id;

        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        if ($homeroomClassIds->isNotEmpty()) {
            if ($homeroomClassIds->count() === 1) {
                $this->class_id = $homeroomClassIds->first();
            } else {
                $this->class_id = null;
            }
        } elseif ($user->hasRole('admin')) {
            $this->class_id = null;
        }
    }

    public function filterForm(Schema $schema): Schema
    {
        $user = Filament::auth()->user();
        $homeroomClassIds = HomeroomTeacher::where('user_id', $user->id)->pluck('class_id');

        $components = [];

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
                TextColumn::make('note')
                    ->label('Catatan')
                    ->limit(100)
                    ->searchable(),
            ])
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(10)
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
            return HomeroomNote::query()->whereRaw('0 = 1');
        }

        return HomeroomNote::query()
            ->select([
                'l.name as learner_name',
                'ay.name as academic_year',
                's.name as semester',
                'hn.note',
            ])
            ->from('homeroom_notes as hn')
            ->join('learners as l', 'l.id', '=', 'hn.learner_id')
            ->join('academic_years as ay', 'ay.id', '=', 'hn.academic_year_id')
            ->join('semesters as s', 's.id', '=', 'hn.semester_id')
            ->when($this->academic_year_id, fn ($q, $v) => $q->where('hn.academic_year_id', $v))
            ->when($this->semester_id, fn ($q, $v) => $q->where('hn.semester_id', $v))
            ->when(! empty($classIds), function ($q) use ($classIds) {
                $learnerIds = ClassLearner::whereIn('class_id', $classIds)->pluck('learner_id');
                $q->whereIn('hn.learner_id', $learnerIds);
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

    public function updatedClassId(): void
    {
        $this->academic_year_id = null;
        $this->semester_id = null;
        $this->resetTable();
    }
}
