<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\PromotionMapping;
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

class ReportPromotions extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-path';

    protected static string|UnitEnum|null $navigationGroup = 'Laporan';

    protected static ?int $navigationSort = 6;

    protected static ?string $title = 'Laporan Kenaikan Kelas';

    protected ?string $heading = 'Laporan Kenaikan Kelas';

    protected string $view = 'filament.pages.report-promotions';

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
                Select::make('academic_year_id')
                    ->label('Tahun Ajaran')
                    ->options(AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                    ->placeholder('Semua'),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                PromotionMapping::query()
                    ->with(['sourceClass', 'destinationClass'])
                    ->when(
                        $this->filters['academic_year_id'] ?? null,
                        fn (Builder $q, $v) => $q->where('academic_year_id', $v)
                    )
            )
            ->columns([
                TextColumn::make('sourceClass.name')->label('Kelas Asal'),
                TextColumn::make('destinationClass.name')->label('Kelas Tujuan'),
                TextColumn::make('academicYear.name')->label('Tahun Ajaran'),
                TextColumn::make('promoted_at')->label('Tanggal Naik')->dateTime(),
                TextColumn::make('notes')->label('Catatan')->limit(50),
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
        $rows = PromotionMapping::query()
            ->with(['sourceClass', 'destinationClass', 'academicYear'])
            ->when(
                $this->filters['academic_year_id'] ?? null,
                fn (Builder $q, $v) => $q->where('academic_year_id', $v)
            )
            ->get();

        return $this->streamCsv('promotions.csv', [
            'Kelas Asal', 'Kelas Tujuan', 'Tahun Ajaran', 'Tanggal Naik', 'Catatan',
        ], $rows, fn ($row) => [
            $row->sourceClass?->name,
            $row->destinationClass?->name,
            $row->academicYear?->name,
            $row->promoted_at?->format('Y-m-d H:i:s'),
            $row->notes,
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
