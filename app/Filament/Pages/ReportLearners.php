<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\Learner;
use App\Models\Program;
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

class ReportLearners extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $title = 'Laporan Peserta Didik';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-academic-cap';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 2;

    protected ?string $heading = 'Laporan Peserta Didik';

    protected string $view = 'filament.pages.report-learners';

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.pages.dashboard') => 'Beranda',
            'Laporan',
            'Laporan Peserta Didik',
        ];
    }

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
                Select::make('program_id')
                    ->label('Program')
                    ->options(Program::pluck('name', 'id'))
                    ->placeholder('Semua Program')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $set('class_id', null);
                        $this->resetTable();
                    }),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'lulus' => 'Lulus',
                        'keluar' => 'Keluar',
                        'pindah' => 'Pindah',
                    ])
                    ->placeholder('Semua Status')
                    ->reactive()
                    ->afterStateUpdated(fn () => $this->resetTable()),
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                    ->placeholder('Semua')
                    ->reactive()
                    ->afterStateUpdated(function (callable $set) {
                        $this->resetTable();
                    }),
                Select::make('class_id')
                    ->label('Kelas')
                    ->reactive()
                    ->options(fn (callable $get) => Classes::when(
                        $get('program_id'),
                        fn (Builder $q, $v) => $q->where('program_id', $v)
                    )->pluck('name', 'id'))
                    ->placeholder('Semua Kelas')
                    ->afterStateUpdated(fn () => $this->resetTable()),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Learner::query()
                    ->when(filled($this->filters['status'] ?? null), fn (Builder $q) => $q->where('status', $this->filters['status']))
                    ->when(filled($this->filters['program_id'] ?? null), fn (Builder $q) => $q->where('program_id', $this->filters['program_id']))
                    ->when(filled($this->filters['academic_year_id'] ?? null), fn (Builder $q) => $q->whereHas(
                        'classLearners', fn (Builder $q) => $q->where('academic_year_id', $this->filters['academic_year_id'])
                    ))
                    ->when(filled($this->filters['class_id'] ?? null), fn (Builder $q) => $q->whereHas(
                        'classLearners', fn (Builder $q) => $q->where('class_id', $this->filters['class_id'])
                    ))
            )
            ->columns([
                TextColumn::make('nis')->label('NIS')->searchable(),
                TextColumn::make('nisn')->label('NISN')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('program.name')->label('Program'),
                TextColumn::make('gender')->label('Jenis Kelamin')->formatStateUsing(fn ($s) => $s === 'L' ? 'Laki-laki' : 'Perempuan'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($s) => match ($s) {
                        'active' => 'success',
                        'alumni' => 'info',
                        'dropped' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($s) => ucfirst($s)),
            ])
            ->headerActions([
                Action::make('exportCsv')
                    ->label('Ekspor Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn () => $this->exportCsv()),
            ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = Learner::query()
            ->with('program')
            ->when(filled($this->filters['status'] ?? null), fn (Builder $q) => $q->where('status', $this->filters['status']))
            ->when(filled($this->filters['program_id'] ?? null), fn (Builder $q) => $q->where('program_id', $this->filters['program_id']))
            ->when(filled($this->filters['academic_year_id'] ?? null), fn (Builder $q) => $q->whereHas(
                'classLearners', fn (Builder $q) => $q->where('academic_year_id', $this->filters['academic_year_id'])
            ))
            ->when(filled($this->filters['class_id'] ?? null), fn (Builder $q) => $q->whereHas(
                'classLearners', fn (Builder $q) => $q->where('class_id', $this->filters['class_id'])
            ))
            ->get();

        return app(ExcelService::class)->exportReport('learners.xlsx', [
            'NIS', 'NISN', 'Nama', 'Program', 'Jenis Kelamin',
            'Tempat Lahir', 'Tanggal Lahir', 'Alamat', 'Status',
        ], $rows, fn ($row) => [
            $row->nis,
            $row->nisn,
            $row->name,
            $row->program?->name,
            $row->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $row->birth_place,
            $row->birth_date?->format('Y-m-d'),
            $row->address,
            $row->status,
        ]);
    }
}
