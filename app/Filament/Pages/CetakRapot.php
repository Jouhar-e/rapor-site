<?php

namespace App\Filament\Pages;

use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\ClassLearner;
use App\Models\Semester;
use App\Services\ReportCardService;
use BackedEnum;
use Filament\Actions\Action as TableAction;
use Filament\Actions\BulkAction;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use UnitEnum;

class CetakRapot extends Page implements HasTable
{
    use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $title = 'Cetak Rapot';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-printer';

    protected static string|UnitEnum|null $navigationGroup = 'Akademik';

    protected static ?int $navigationSort = 99;

    protected ?string $heading = 'Cetak Rapot';

    protected string $view = 'filament.pages.cetak-rapot';

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
                    ->options(AcademicYear::where('is_archived', false)->pluck('name', 'id'))
                    ->placeholder('Pilih Tahun Ajaran')
                    ->live()
                    ->afterStateUpdated(function (callable $set) {
                        $set('semester_id', null);
                        $set('class_id', null);
                        $this->resetTable();
                    }),
                Select::make('semester_id')
                    ->label('Semester')
                    ->live()
                    ->options(fn (callable $get) => Semester::when(
                        $get('academic_year_id'),
                        fn (Builder $q, $v) => $q->where('academic_year_id', $v)
                    )->pluck('name', 'id'))
                    ->placeholder('Pilih Semester')
                    ->afterStateUpdated(function (callable $set) {
                        $set('class_id', null);
                        $this->resetTable();
                    }),
                Select::make('class_id')
                    ->label('Kelas')
                    ->live()
                    ->options(fn (callable $get) => Classes::when(
                        $get('academic_year_id'),
                        fn (Builder $q, $v) => $q->whereHas('classLearners', fn (Builder $q) => $q->where('academic_year_id', $v))
                    )->pluck('name', 'id'))
                    ->placeholder('Pilih Kelas')
                    ->afterStateUpdated(fn () => $this->resetTable()),
                CheckboxList::make('sections')
                    ->label('Bagian yang Dicetak')
                    ->options([
                        'cover' => 'Cover (Halaman 1)',
                        'identitas' => 'Identitas Sekolah (Halaman 2)',
                        'biodata' => 'Biodata Peserta Didik (Halaman 3)',
                        'nilai' => 'Nilai Akademik (Halaman 4+)',
                    ])
                    ->columns(2)
                    ->default(['cover', 'identitas', 'biodata', 'nilai'])
                    ->live(),
            ])
            ->statePath('filters');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ClassLearner::query()
                    ->with('learner')
                    ->when(
                        $this->filters['academic_year_id'] ?? null,
                        fn (Builder $q, $v) => $q->where('academic_year_id', $v)
                    )
                    ->when(
                        $this->filters['semester_id'] ?? null,
                        fn (Builder $q, $v) => $q->where('semester_id', $v)
                    )
                    ->when(
                        $this->filters['class_id'] ?? null,
                        fn (Builder $q, $v) => $q->where('class_id', $v)
                    )
            )
            ->columns([
                TextColumn::make('learner.nis')->label('NIS')->searchable(),
                TextColumn::make('learner.nisn')->label('NISN')->searchable(),
                TextColumn::make('learner.name')->label('Nama')->searchable(),
            ])
            ->recordUrl(null)
            ->actions([
                TableAction::make('cetak')
                    ->label('Cetak Rapot')
                    ->icon('heroicon-o-printer')
                    ->action(fn (ClassLearner $record) => $this->cetakPerorangan($record)),
            ])
            ->bulkActions([
                BulkAction::make('cetakTerpilih')
                    ->label('Cetak Terpilih')
                    ->icon('heroicon-o-document-arrow-down')
                    ->action(fn (Collection $records) => $this->cetakMassal($records)),
            ]);
    }

    public function cetakPerorangan(ClassLearner $record): mixed
    {
        $sections = $this->filters['sections'] ?? ['cover', 'identitas', 'biodata', 'nilai'];

        return app(ReportCardService::class)->generatePdf(
            $record->learner_id,
            $record->academic_year_id,
            $record->semester_id,
            $sections,
        );
    }

    public function cetakMassal(Collection $records): mixed
    {
        $sections = $this->filters['sections'] ?? ['cover', 'identitas', 'biodata', 'nilai'];

        $learnerIds = $records->pluck('learner_id')->values()->toArray();

        if (count($learnerIds) === 0) {
            Notification::make()
                ->warning()
                ->title('Tidak ada peserta dipilih')
                ->send();

            return null;
        }

        if (count($learnerIds) === 1) {
            $record = $records->first();

            return app(ReportCardService::class)->generatePdf(
                $record->learner_id,
                $record->academic_year_id,
                $record->semester_id,
                $sections,
            );
        }

        $firstRecord = $records->first();

        return app(ReportCardService::class)->generateZip(
            $learnerIds,
            $firstRecord->academic_year_id,
            $firstRecord->semester_id,
            $sections,
        );
    }
}
