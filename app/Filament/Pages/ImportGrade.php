<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Grades\GradeResource;
use App\Models\AcademicYear;
use App\Models\Classes;
use App\Models\HomeroomTeacher;
use App\Models\ImportHistory;
use App\Models\Learner;
use App\Models\Semester;
use App\Models\Subject;
use App\Services\ImportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportGrade extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Import Nilai';

    protected ?string $heading = 'Import Nilai';

    protected string $view = 'filament.pages.import-grade';

    public static function canAccess(): bool
    {
        return auth()->user()->can('import.grade');
    }

    public ?array $data = [];

    public ?array $previewData = null;

    public ?array $columnMap = [];

    public ?array $importResult = null;

    public int $step = 1;

    public int $previewKey = 0;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        Select::make('academic_year_id')
                            ->label('Tahun Ajaran')
                            ->options(AcademicYear::where('is_archived', false)->where('is_active', true)->pluck('name', 'id'))
                            ->required(),
                        Select::make('semester_id')
                            ->label('Semester')
                            ->options(fn () => Semester::whereHas('academicYear', fn ($q) => $q->where('is_archived', false)->where('is_active', true))->pluck('name', 'id'))
                            ->required(),
                        Select::make('subject_id')
                            ->label('Mata Pelajaran')
                            ->options(Subject::pluck('name', 'id'))
                            ->required(),
                    ]),
                FileUpload::make('file')
                    ->label('File Excel (.xlsx)')
                    ->acceptedFileTypes([
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(10240)
                    ->storeFiles(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function preview(): void
    {
        $this->validate();

        $state = $this->form->getState();
        $file = $state['file'] ?? null;

        if (! $file) {
            return;
        }

        $this->previewXlsx($file, $state);
    }

    protected function previewXlsx($file, array $state): void
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            $this->addError('data.file', 'File Excel kosong.');

            return;
        }

        $headers = array_map('strval', array_map('trim', $rows[0]));
        array_shift($rows);

        $required = ['nis', 'name'];
        $missing = array_diff($required, $headers);
        if (! empty($missing)) {
            $this->addError('data.file', 'Kolom wajib tidak ditemukan: '.implode(', ', $missing));

            return;
        }

        $headerCount = count($headers);
        $records = [];

        foreach ($rows as $row) {
            if (count($row) < $headerCount) {
                $row = array_pad($row, $headerCount, null);
            }
            $record = array_combine($headers, array_slice($row, 0, $headerCount));
            $record = array_map(fn ($v) => $v !== null ? trim((string) $v) : '', $record);
            if (! empty($record['nis']) || ! empty($record['name'])) {
                $records[] = $record;
            }
        }

        $this->finalizePreview($records, $headers, $state);
    }

    protected function finalizePreview(array $records, array $headers, array $state): void
    {
        $this->previewData = array_slice($records, 0, 10);
        $this->previewKey++;

        session([
            'import_grade_data' => $records,
            'import_grade_headers' => $headers,
            'import_grade_academic_year_id' => $state['academic_year_id'],
            'import_grade_semester_id' => $state['semester_id'],
            'import_grade_subject_id' => $state['subject_id'],
        ]);

        $this->step = 2;
    }

    public function executeImport(): void
    {
        $records = session('import_grade_data', []);

        if (empty($records)) {
            return;
        }

        $academicYearId = session('import_grade_academic_year_id');
        $semesterId = session('import_grade_semester_id');
        $subjectId = session('import_grade_subject_id');

        $mapped = [];
        $nisList = collect($records)->pluck('nis')->filter()->unique()->values()->all();
        $learners = Learner::whereIn('nis', $nisList)->get()->keyBy('nis');

        foreach ($records as $row) {
            $nis = $row['nis'] ?? '';
            $learner = $learners->get($nis);

            $toNull = fn ($v) => $v === '' || $v === null ? 0 : $v;

            $mapped[] = [
                'learner_id' => $learner?->id,
                'nis' => $nis,
                'subject_id' => $subjectId,
                'academic_year_id' => $academicYearId,
                'semester_id' => $semesterId,
                'task_score' => $toNull($row['task_score'] ?? null),
                'pts_score' => $toNull($row['pts_score'] ?? null),
                'pas_score' => $toNull($row['pas_score'] ?? null),
                'practice_score' => $toNull($row['practice_score'] ?? null),
                'competency_description' => $row['competency_description'] ?? null,
            ];
        }

        $service = app(ImportService::class);
        $result = $service->importGrades($mapped);

        ImportHistory::create([
            'type' => 'grade',
            'file_name' => $this->form->getState()['file']?->getClientOriginalName() ?? '',
            'total_rows' => $result->total(),
            'imported' => $result->imported,
            'updated' => $result->updated,
            'skipped' => $result->skipped,
            'errors' => $result->errors,
            'created_by' => Auth::id(),
        ]);

        $this->importResult = [
            'success' => $result->success,
            'imported' => $result->imported,
            'updated' => $result->updated,
            'skipped' => $result->skipped,
            'errors' => $result->errors,
            'total' => $result->total(),
        ];

        session()->forget([
            'import_grade_data', 'import_grade_headers',
            'import_grade_academic_year_id', 'import_grade_semester_id', 'import_grade_subject_id',
        ]);
        $this->step = 3;
    }

    public function resetImport(): void
    {
        $this->previewData = null;
        $this->columnMap = [];
        $this->importResult = null;
        $this->step = 1;
        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ImportHistory::query()->where('type', 'grade'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('file_name')
                    ->label('File')
                    ->searchable(),
                TextColumn::make('total_rows')
                    ->label('Total')
                    ->alignCenter(),
                TextColumn::make('imported')
                    ->label('Baru')
                    ->badge()
                    ->color('success')
                    ->alignCenter(),
                TextColumn::make('updated')
                    ->label('Diperbarui')
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                TextColumn::make('skipped')
                    ->label('Dilewati')
                    ->badge()
                    ->color('warning')
                    ->alignCenter(),
                TextColumn::make('errors')
                    ->label('Gagal')
                    ->state(fn (ImportHistory $record): int => count($record->errors ?? []))
                    ->badge()
                    ->color(fn (ImportHistory $record): string => count($record->errors ?? []) > 0 ? 'danger' : 'gray')
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $myClassIds = HomeroomTeacher::where('user_id', Auth::id())->pluck('class_id');

        if ($myClassIds->count() === 1) {
            $actions[] = Action::make('downloadTemplate')
                ->label('Unduh Format Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->url(route('grade.template', ['class' => $myClassIds->first()]));
        } else {
            $options = $myClassIds->isNotEmpty()
                ? Classes::whereIn('id', $myClassIds)->pluck('name', 'id')
                : Classes::pluck('name', 'id');

            $actions[] = Action::make('downloadTemplate')
                ->label('Unduh Format Excel')
                ->icon('heroicon-o-document-arrow-down')
                ->color('success')
                ->form([
                    Select::make('class_id')
                        ->label('Pilih Kelas')
                        ->options($options)
                        ->required(),
                ])
                ->action(function (array $data) {
                    $this->redirect(route('grade.template', ['class' => $data['class_id']]));
                });
        }

        $actions[] = Action::make('back')
            ->label('Kembali ke Input Nilai')
            ->icon('heroicon-o-arrow-left')
            ->url(fn (): string => GradeResource::getUrl('manage'))
            ->color('gray');

        return $actions;
    }
}
