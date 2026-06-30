<?php

namespace App\Filament\Pages;

use App\Models\Program;
use App\Models\Tutor;
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

class ReportTutors extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $title = 'Laporan Tutor';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 1;

    protected ?string $heading = 'Laporan Tutor';

    protected string $view = 'filament.pages.report-tutors';

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
                Select::make('status')
                    ->label('Status')
                    ->options([
                        '1' => 'Aktif',
                        '0' => 'Nonaktif',
                    ])
                    ->placeholder('Semua Status'),
                Select::make('program_id')
                    ->label('Program')
                    ->options(Program::pluck('name', 'id'))
                    ->placeholder('Semua Program'),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Tutor::query()
                    ->when($this->filters['status'] ?? null, fn (Builder $q, $v) => $q->where('is_active', $v))
                    ->when($this->filters['program_id'] ?? null, function (Builder $q, $v) {
                        $q->whereHas('homeroomTeachers.classes', fn (Builder $q) => $q->where('program_id', $v));
                    })
            )
            ->columns([
                TextColumn::make('nip')->label('NIP')->searchable(),
                TextColumn::make('name')->label('Nama')->searchable(),
                TextColumn::make('gender')->label('Jenis Kelamin')->formatStateUsing(fn ($s) => $s === 'L' ? 'Laki-laki' : 'Perempuan'),
                TextColumn::make('phone')->label('Telepon'),
                TextColumn::make('email')->label('Email'),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($s) => $s ? 'success' : 'danger')
                    ->formatStateUsing(fn ($s) => $s ? 'Aktif' : 'Nonaktif'),
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
        $rows = Tutor::query()
            ->when($this->filters['status'] ?? null, fn (Builder $q, $v) => $q->where('is_active', $v))
            ->when($this->filters['program_id'] ?? null, function (Builder $q, $v) {
                $q->whereHas('homeroomTeachers.classes', fn (Builder $q) => $q->where('program_id', $v));
            })
            ->get();

        return $this->streamCsv('tutors.csv', [
            'NIP', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir',
            'Alamat', 'Telepon', 'Email', 'Status',
        ], $rows, fn ($row) => [
            $row->nip,
            $row->name,
            $row->gender === 'L' ? 'Laki-laki' : 'Perempuan',
            $row->birth_place,
            $row->birth_date?->format('Y-m-d'),
            $row->address,
            $row->phone,
            $row->email,
            $row->is_active ? 'Aktif' : 'Nonaktif',
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
