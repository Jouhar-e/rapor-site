<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Subjects\SubjectResource;
use App\Models\Classes;
use App\Models\ImportHistory;
use App\Models\Subject;
use App\Services\ExcelService;
use App\Services\ImportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportSubject extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Import Mata Pelajaran';

    protected ?string $heading = 'Import Mata Pelajaran';

    protected string $view = 'filament.pages.import-subject';

    public static function canAccess(): bool
    {
        return auth()->user()->can('import.subject');
    }

    public ?array $data = ['class_id' => null];

    public ?array $previewData = null;

    public ?array $columnMap = [];

    public ?array $importResult = null;

    public int $step = 1;

    public int $previewKey = 0;

    public ?int $selectedClassId = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('class_id')
                    ->label('Kelas')
                    ->options(fn () => Classes::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function (): void {
                        $this->previewData = null;
                        $this->columnMap = [];
                        $this->importResult = null;
                        $this->step = 1;
                        $this->selectedClassId = null;
                    }),
                FileUpload::make('file')
                    ->label('File Excel (.xlsx)')
                    ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                    ->maxSize(2048)
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
        $classId = $state['class_id'] ?? null;

        if (! $file || ! $classId) {
            return;
        }

        $this->selectedClassId = (int) $classId;

        $spreadsheet = IOFactory::load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if (empty($rows)) {
            $this->addError('data.file', 'File Excel kosong.');

            return;
        }

        $headers = array_map('strval', array_map('trim', $rows[0]));
        array_shift($rows);

        if (! in_array('name', $headers)) {
            $this->addError('data.file', 'Kolom "name" wajib ada di file Excel.');

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
            if (! empty($record['name'])) {
                $records[] = $record;
            }
        }

        $this->previewData = array_slice($records, 0, 10);
        $this->columnMap = array_combine($headers, $headers);
        $this->previewKey++;

        session(['import_subject_data' => $records]);
        session(['import_subject_headers' => $headers]);

        $this->step = 2;
    }

    public function executeImport(): void
    {
        $records = session('import_subject_data', []);
        $headers = session('import_subject_headers', []);

        if (empty($records) || ! $this->selectedClassId) {
            return;
        }

        $classId = $this->selectedClassId;
        $className = Classes::find($classId)?->name ?? '';
        $classNum = '';
        preg_match('/(\d+)/', $className, $matches);
        $classNum = $matches[1] ?? '';

        $usedCodes = Subject::where('class_id', $classId)->pluck('code')->toArray();

        $mapped = [];
        foreach ($records as $row) {
            $name = trim($row['name'] ?? '');
            if (empty($name)) {
                continue;
            }

            $existing = Subject::where('class_id', $classId)->where('name', $name)->first();

            if ($existing) {
                $code = $existing->code;
            } else {
                $prefix = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name), 0, 3));
                $baseCode = $prefix.($classNum ? '-'.$classNum : '');

                $code = $baseCode;
                $counter = 1;
                while (in_array($code, $usedCodes)) {
                    $code = $baseCode.'_'.$counter;
                    $counter++;
                }
                $usedCodes[] = $code;
            }

            $subjectGroupName = $row['subject_group'] ?? '';

            $mapped[] = [
                'name' => $name,
                'code' => $code,
                'class_id' => $classId,
                'subject_group_name' => $subjectGroupName,
                'description' => $row['description'] ?? '',
                'is_active' => filter_var($row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
            ];
        }

        $service = app(ImportService::class);
        $result = $service->importSubjects($mapped);

        ImportHistory::create([
            'type' => 'subject',
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

        session()->forget(['import_subject_data', 'import_subject_headers']);
        $this->step = 3;
    }

    public function resetImport(): void
    {
        $this->previewData = null;
        $this->columnMap = [];
        $this->importResult = null;
        $this->step = 1;
        $this->selectedClassId = null;
        $this->data['file'] = null;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(ImportHistory::query()->where('type', 'subject'))
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

    public function downloadTemplate(): StreamedResponse
    {
        return app(ExcelService::class)->downloadSubjectTemplate();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Unduh Format Excel')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadTemplate')
                ->color('gray'),
            Action::make('back')
                ->label('Kembali ke Data Mata Pelajaran')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => SubjectResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
