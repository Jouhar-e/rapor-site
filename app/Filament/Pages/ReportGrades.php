<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
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

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    public ?array $filters = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function updated($propertyName): void
    {
        if (str_starts_with($propertyName, 'filters.')) {
            $this->resetTable();
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                    ->placeholder('Semua')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('semester_id', null);
                        $this->resetTable();
                    }),
                Select::make('semester_id')
                    ->label('Semester')
                    ->reactive()
                    ->options(fn (callable $get) => Semester::when(
                        $get('academic_year_id'),
                        fn (Builder $q, $v) => $q->where('academic_year_id', $v)
                    )->whereHas('academicYear', fn ($q) => $q->where('is_archived', false))->pluck('name', 'id'))
                    ->placeholder('Semua')
                    ->afterStateUpdated(fn () => $this->resetTable()),
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(Classes::pluck('name', 'id'))
                    ->placeholder('Semua')
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->resetTable()),
                Select::make('subject_id')
                    ->label('Mata Pelajaran')
                    ->options(Subject::pluck('name', 'id'))
                    ->placeholder('Semua')
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->resetTable()),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Grade::query()
                    ->with(['learner', 'subject', 'academicYear', 'semester'])
                    ->when($this->filters['academic_year_id'] ?? null, fn (Builder $q, $v) => $q->where('academic_year_id', $v))
                    ->when($this->filters['semester_id'] ?? null, fn (Builder $q, $v) => $q->where('semester_id', $v))
                    ->when($this->filters['subject_id'] ?? null, fn (Builder $q, $v) => $q->where('subject_id', $v))
                    ->when($this->filters['class_id'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                        'learner.classLearners', fn (Builder $q) => $q->where('class_id', $v)
                    ))
            )
            ->columns([
                TextColumn::make('learner.name')->label('Nama')->searchable(),
                TextColumn::make('subject.name')->label('Mata Pelajaran'),
                TextColumn::make('final_score')->label('Nilai Akhir')->sortable(),
                TextColumn::make('predicate')->label('Predikat')->badge(),
                TextColumn::make('academicYear.name')->label('Tahun Ajaran'),
                TextColumn::make('semester.name')->label('Semester'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($s) => $s === 'published' ? 'success' : 'warning'),
            ])
            ->headerActions([
                Action::make('exportCsv')
                    ->label('Ekspor Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => $this->exportCsv()),
            ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = Grade::query()
            ->with(['learner', 'subject', 'academicYear', 'semester'])
            ->when($this->filters['academic_year_id'] ?? null, fn (Builder $q, $v) => $q->where('academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn (Builder $q, $v) => $q->where('semester_id', $v))
            ->when($this->filters['subject_id'] ?? null, fn (Builder $q, $v) => $q->where('subject_id', $v))
            ->when($this->filters['class_id'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                'learner.classLearners', fn (Builder $q) => $q->where('class_id', $v)
            ))
            ->get();

        return app(ExcelService::class)->exportReport('grades.xlsx', [
            'Nama Warga Belajar', 'Mata Pelajaran', 'Nilai Tugas', 'Nilai PTS',
            'Nilai PAS', 'Nilai Praktik', 'Nilai Akhir', 'Predikat',
            'Tahun Ajaran', 'Semester', 'Status',
        ], $rows, fn ($row) => [
            $row->learner?->name,
            $row->subject?->name,
            $row->task_score,
            $row->pts_score,
            $row->pas_score,
            $row->practice_score,
            $row->final_score,
            $row->predicate,
            $row->academicYear?->name,
            $row->semester?->name,
            $row->status,
        ]);
    }
}
