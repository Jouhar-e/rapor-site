<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Learners\LearnerResource;
use App\Models\ImportHistory;
use App\Services\ImportService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ImportLearner extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Import Peserta Didik';

    protected ?string $heading = 'Import Peserta Didik';

    protected string $view = 'filament.pages.import-learner';

    public static function canAccess(): bool
    {
        return auth()->user()->can('import.learner');
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
                FileUpload::make('file')
                    ->label('File CSV')
                    ->acceptedFileTypes(['text/csv', 'text/plain', 'application/vnd.ms-excel'])
                    ->maxSize(2048)
                    ->storeFiles(false)
                    ->required(),
            ])
            ->statePath('data');
    }

    public function preview(): void
    {
        $this->validate();

        $file = $this->form->getState()['file'] ?? null;

        if (! $file) {
            return;
        }

        $path = $file->getRealPath();
        $lines = file($path);

        if ($lines === false || empty($lines)) {
            $this->addError('data.file', 'File CSV kosong.');

            return;
        }

        $firstLine = trim(array_shift($lines));
        $headers = str_getcsv($firstLine);
        $headerCount = count($headers);
        $required = ['nis', 'nisn', 'name', 'gender', 'birth_place', 'birth_date', 'address', 'status', 'class_name', 'religion', 'child_order', 'phone', 'admission_date', 'admission_class', 'admission_status', 'father_name', 'father_job', 'mother_name', 'mother_job', 'guardian_name', 'guardian_job', 'report_number'];

        $missing = array_diff($required, $headers);
        if (! empty($missing)) {
            $this->addError('data.file', 'Kolom wajib tidak ditemukan: '.implode(', ', $missing));

            return;
        }

        $records = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $parsed = false;

            foreach ([',', ';'] as $delimiter) {
                $row = str_getcsv($line, $delimiter);

                if (count($row) === $headerCount) {
                    $records[] = array_combine($headers, $row);
                    $parsed = true;
                    break;
                }
            }

            if (! $parsed && $line !== '' && $line[0] === '"' && $line[-1] === '"') {
                $inner = str_replace('""', '"', substr($line, 1, -1));
                $row = str_getcsv($inner);

                if (count($row) === $headerCount) {
                    $records[] = array_combine($headers, $row);
                }
            }
        }

        $this->previewData = array_slice($records, 0, 10);
        $this->columnMap = array_combine($headers, $headers);
        $this->previewKey++;

        session(['import_learner_data' => $records]);
        session(['import_learner_headers' => $headers]);

        $this->step = 2;
    }

    public function executeImport(): void
    {
        $records = session('import_learner_data', []);
        $headers = session('import_learner_headers', []);

        if (empty($records)) {
            return;
        }

        $mapped = [];
        foreach ($records as $row) {
            $mapped[] = [
                'nis' => $row['nis'] ?? '',
                'nisn' => $row['nisn'] ?? '',
                'name' => $row['name'] ?? '',
                'gender' => $row['gender'] ?? '',
                'birth_place' => $row['birth_place'] ?? '',
                'birth_date' => $row['birth_date'] ?? '',
                'address' => $row['address'] ?? '',
                'status' => $row['status'] ?? '',
                'class_name' => $row['class_name'] ?? '',
                'religion' => $row['religion'] ?? null,
                'child_order' => $row['child_order'] ?? null,
                'phone' => $row['phone'] ?? null,
                'admission_date' => $row['admission_date'] ?? null,
                'admission_class' => $row['admission_class'] ?? null,
                'admission_status' => $row['admission_status'] ?? null,
                'father_name' => $row['father_name'] ?? null,
                'father_job' => $row['father_job'] ?? null,
                'mother_name' => $row['mother_name'] ?? null,
                'mother_job' => $row['mother_job'] ?? null,
                'guardian_name' => $row['guardian_name'] ?? null,
                'guardian_job' => $row['guardian_job'] ?? null,
                'report_number' => $row['report_number'] ?? null,
            ];
        }

        $service = app(ImportService::class);
        $result = $service->importLearners($mapped);

        ImportHistory::create([
            'type' => 'learner',
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

        session()->forget(['import_learner_data', 'import_learner_headers']);
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
            ->query(ImportHistory::query()->where('type', 'learner'))
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
        $headers = ['nis', 'nisn', 'name', 'gender', 'birth_place', 'birth_date', 'address', 'status', 'class_name', 'religion', 'child_order', 'phone', 'admission_date', 'admission_class', 'admission_status', 'father_name', 'father_job', 'mother_name', 'mother_job', 'guardian_name', 'guardian_job', 'report_number'];
        $sample = ['12345', '1234567890', 'Siti Aisyah', 'P', 'Jakarta', '2010-05-10', 'Jl. Merdeka No.1', 'aktif', 'Kelas X.1', 'Islam', '1', '08123456789', '2024-07-15', 'Paket A', 'baru', 'Ahmad', 'Petani', 'Siti', 'Ibu Rumah Tangga', '', '', ''];

        $callback = function () use ($headers, $sample): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, $headers);
            fputcsv($handle, $sample);
            fclose($handle);
        };

        return response()->streamDownload($callback, 'format-import-peserta.csv', ['Content-Type' => 'text/csv']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('downloadTemplate')
                ->label('Unduh Format CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->action('downloadTemplate')
                ->color('gray'),
            Action::make('back')
                ->label('Kembali ke Data Peserta Didik')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => LearnerResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
