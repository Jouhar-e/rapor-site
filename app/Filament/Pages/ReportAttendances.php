<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\Classes;
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
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Attendance::query()
                    ->select([
                        'learner_id',
                        DB::raw('SUM(sick) as total_sick'),
                        DB::raw('SUM(permission) as total_permission'),
                        DB::raw('SUM(absent) as total_absent'),
                    ])
                    ->with('learner')
                    ->when($this->filters['academic_year_id'] ?? null, fn (Builder $q, $v) => $q->where('academic_year_id', $v))
                    ->when($this->filters['semester_id'] ?? null, fn (Builder $q, $v) => $q->where('semester_id', $v))
                    ->when($this->filters['class_id'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                        'learner.classLearners', fn (Builder $q) => $q->where('class_id', $v)
                    ))
                    ->groupBy('learner_id')
            )
            ->columns([
                TextColumn::make('learner.nis')->label('NIS')->searchable(),
                TextColumn::make('learner.name')->label('Nama')->searchable(),
                TextColumn::make('total_sick')->label('Sakit')->sortable(),
                TextColumn::make('total_permission')->label('Izin')->sortable(),
                TextColumn::make('total_absent')->label('Tanpa Keterangan')->sortable(),
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
        $rows = Attendance::query()
            ->select([
                'learner_id',
                DB::raw('SUM(sick) as total_sick'),
                DB::raw('SUM(permission) as total_permission'),
                DB::raw('SUM(absent) as total_absent'),
            ])
            ->with('learner')
            ->when($this->filters['academic_year_id'] ?? null, fn (Builder $q, $v) => $q->where('academic_year_id', $v))
            ->when($this->filters['semester_id'] ?? null, fn (Builder $q, $v) => $q->where('semester_id', $v))
            ->when($this->filters['class_id'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                'learner.classLearners', fn (Builder $q) => $q->where('class_id', $v)
            ))
            ->groupBy('learner_id')
            ->get();

        return app(ExcelService::class)->exportReport('attendances.xlsx', [
            'NIS', 'Nama', 'Sakit', 'Izin', 'Tanpa Keterangan',
        ], $rows, fn ($row) => [
            $row->learner?->nis,
            $row->learner?->name,
            (int) $row->total_sick,
            (int) $row->total_permission,
            (int) $row->total_absent,
        ]);
    }

    public function getTableRecordKey(Model|array $record): string
    {
        if ($record instanceof Model) {
            return $record->getKey() ?? spl_object_id($record);
        }

        return parent::getTableRecordKey($record);
    }
}
