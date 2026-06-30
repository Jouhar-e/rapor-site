<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Tutors\TutorResource;
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

class ImportTutor extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Import Tutor';

    protected ?string $heading = 'Import Tutor';

    protected string $view = 'filament.pages.import-tutor';

    public static function canAccess(): bool
    {
        return auth()->user()->can('import.tutor');
    }

    public ?array $data = [];

    public ?array $previewData = null;

    public ?array $columnMap = [];

    public ?array $importResult = null;

    public ?int $importHistoryId = null;

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

        $lines = file($file->getRealPath());

        if ($lines === false || empty($lines)) {
            $this->addError('data.file', 'File CSV kosong.');

            return;
        }

        $firstLine = trim(array_shift($lines));
        $headers = str_getcsv($firstLine);
        $headerCount = count($headers);
        $required = ['nip', 'name', 'email'];

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

        session(['import_tutor_data' => $records]);
        session(['import_tutor_headers' => $headers]);

        $this->step = 2;
    }

    public function executeImport(): void
    {
        $records = session('import_tutor_data', []);
        $headers = session('import_tutor_headers', []);

        if (empty($records)) {
            return;
        }

        $mapped = [];
        foreach ($records as $row) {
            $mapped[] = [
                'nip' => $row['nip'] ?? '',
                'name' => $row['name'] ?? '',
                'gender' => $row['gender'] ?? 'L',
                'birth_place' => $row['birth_place'] ?? '',
                'birth_date' => $row['birth_date'] ?? null,
                'address' => $row['address'] ?? '',
                'phone' => $row['phone'] ?? '',
                'email' => $row['email'] ?? '',
                'is_active' => filter_var($row['is_active'] ?? true, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? true,
                'password' => $row['password'] ?? 'password123',
            ];
        }

        $service = app(ImportService::class);
        $result = $service->importTutors($mapped);

        ImportHistory::create([
            'type' => 'tutor',
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

        session()->forget(['import_tutor_data', 'import_tutor_headers']);
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
            ->query(ImportHistory::query()->where('type', 'tutor'))
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
        $headers = ['nip', 'name', 'gender', 'birth_place', 'birth_date', 'address', 'phone', 'email', 'is_active', 'password'];
        $sample = ['T001', 'Budi Santoso', 'L', 'Jakarta', '1990-01-15', 'Jl. Merdeka No.1', '081234567890', 'budi@example.com', '1', 'password123'];

        $callback = function () use ($headers, $sample): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, $headers);
            fputcsv($handle, $sample);
            fclose($handle);
        };

        return response()->streamDownload($callback, 'format-import-tutor.csv', ['Content-Type' => 'text/csv']);
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
                ->label('Kembali ke Data Tutor')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => TutorResource::getUrl('index'))
                ->color('gray'),
        ];
    }
}
