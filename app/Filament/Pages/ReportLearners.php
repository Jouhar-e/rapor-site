<?php

namespace App\Filament\Pages;

use App\Models\Classes;
use App\Models\Learner;
use App\Models\Program;
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

    public static function canAccess(): bool
    {
        return auth()->user()->can('report.view');
    }

    public ?array $filters = [];

    public function mount(): void
    {
        $this->form->fill();
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
                    ->afterStateUpdated(fn (callable $set) => $set('class_id', null)),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'active' => 'Aktif',
                        'alumni' => 'Alumni',
                        'dropped' => 'Keluar',
                    ])
                    ->placeholder('Semua Status'),
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(fn (callable $get) => Classes::when(
                        $get('program_id'),
                        fn (Builder $q, $v) => $q->where('program_id', $v)
                    )->pluck('name', 'id'))
                    ->placeholder('Semua Kelas'),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Learner::query()
                    ->when($this->filters['status'] ?? null, fn (Builder $q, $v) => $q->where('status', $v))
                    ->when($this->filters['program_id'] ?? null, fn (Builder $q, $v) => $q->where('program_id', $v))
                    ->when($this->filters['class_id'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                        'classLearners', fn (Builder $q) => $q->where('class_id', $v)
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
                    ->label('Ekspor CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(fn () => $this->exportCsv()),
            ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $rows = Learner::query()
            ->with('program')
            ->when($this->filters['status'] ?? null, fn (Builder $q, $v) => $q->where('status', $v))
            ->when($this->filters['program_id'] ?? null, fn (Builder $q, $v) => $q->where('program_id', $v))
            ->when($this->filters['class_id'] ?? null, fn (Builder $q, $v) => $q->whereHas(
                'classLearners', fn (Builder $q) => $q->where('class_id', $v)
            ))
            ->get();

        return $this->streamCsv('learners.csv', [
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

    protected function streamCsv(string $filename, array $headers, iterable $rows, callable $mapper): StreamedResponse
    {
        return response()->streamDownload(function () use ($headers, $rows, $mapper): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, $headers);
            foreach ($rows as $row) {
                fputcsv($handle, $mapper($row));
            }
            fclose($handle);
        }, $filename);
    }
}
