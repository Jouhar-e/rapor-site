<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Extracurricular;
use App\Models\LearnerExtracurricular;
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
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ReportExtracurriculars extends Page implements HasTable
{
    use InteractsWithForms;
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
                Select::make('extracurricular_id')
                    ->label('Ekstrakurikuler')
                    ->options(Extracurricular::orderBy('name')->pluck('name', 'id'))
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
                LearnerExtracurricular::query()
                    ->with(['learner', 'extracurricular', 'academicYear', 'semester'])
                    ->when($this->filters['academic_year_id'] ?? null, fn (Builder $q, $v) => $q->where('academic_year_id', $v))
                    ->when($this->filters['semester_id'] ?? null, fn (Builder $q, $v) => $q->where('semester_id', $v))
                    ->when($this->filters['extracurricular_id'] ?? null, fn (Builder $q, $v) => $q->where('extracurricular_id', $v))
            )
            ->columns([
                TextColumn::make('learner.name')->label('Nama Warga Belajar')->searchable(),
                TextColumn::make('extracurricular.name')->label('Ekstrakurikuler'),
                TextColumn::make('academicYear.name')->label('Tahun Ajaran'),
                TextColumn::make('semester.name')->label('Semester'),
                TextColumn::make('notes')->label('Catatan')->limit(50),
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
        $rows = LearnerExtracurricular::query()
            ->with(['learner', 'extracurricular', 'academicYear', 'semester'])
            ->when($this->filters['academic_year_id'] ?? null, fn (Builder $q, $v) => $q->where('academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn (Builder $q, $v) => $q->where('semester_id', $v))
            ->when($this->filters['extracurricular_id'] ?? null, fn (Builder $q, $v) => $q->where('extracurricular_id', $v))
            ->get();

        return app(ExcelService::class)->exportReport('extracurriculars.xlsx', [
            'Nama Warga Belajar', 'Ekstrakurikuler', 'Tahun Ajaran', 'Semester', 'Catatan',
        ], $rows, fn ($row) => [
            $row->learner?->name,
            $row->extracurricular?->name,
            $row->academicYear?->name,
            $row->semester?->name,
            $row->notes,
        ]);
    }
}
