<?php

namespace App\Filament\Pages;

use App\Filament\Resources\BackupHistories\BackupHistoryResource;
use App\Models\BackupHistory;
use App\Services\AuditService;
use App\Services\BackupService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class RestoreBackup extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-up-tray';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $title = 'Restore Backup';

    protected ?string $heading = 'Restore Backup';

    protected string $view = 'filament.pages.restore-backup';

    public static function canAccess(): bool
    {
        return auth()->user()->can('backup.restore');
    }

    public ?array $data = [];

    public ?array $fileInfo = null;

    public ?array $restoreResult = null;

    public int $step = 1;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('file')
                    ->label('File Backup (.sql)')
                    ->rules(['mimes:sql', 'max:51200'])
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

        $this->fileInfo = [
            'name' => $file->getClientOriginalName(),
            'size' => $file->getSize(),
            'size_formatted' => $this->formatBytes($file->getSize()),
            'uploaded_at' => now()->format('d/m/Y H:i'),
        ];

        $this->step = 2;
    }

    public function executeRestore(): void
    {
        $file = session('restore_backup_file');

        if (! $file) {
            $fileState = $this->form->getState()['file'] ?? null;
            if (! $fileState) {
                Notification::make()
                    ->danger()
                    ->title('Restore gagal')
                    ->body('File tidak ditemukan.')
                    ->send();

                return;
            }
            $file = $fileState->getRealPath();
        }

        try {
            app(BackupService::class)->restore($file);

            $filename = $this->fileInfo['name'] ?? pathinfo($file, PATHINFO_BASENAME);
            app(AuditService::class)->logBackup('restore', $filename);

            $this->restoreResult = [
                'success' => true,
                'filename' => $filename,
            ];

            Notification::make()
                ->success()
                ->title('Restore berhasil')
                ->body("Data berhasil direstore dari {$filename}")
                ->send();
        } catch (\Exception $e) {
            $this->restoreResult = [
                'success' => false,
                'error' => $e->getMessage(),
            ];

            Notification::make()
                ->danger()
                ->title('Restore gagal')
                ->body($e->getMessage())
                ->send();
        }

        session()->forget('restore_backup_file');
        $this->step = 3;
    }

    public function resetRestore(): void
    {
        $this->fileInfo = null;
        $this->restoreResult = null;
        $this->step = 1;
        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(BackupHistory::query()->where('type', 'database')->where('status', 'completed'))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('filename')
                    ->label('File')
                    ->searchable(),
                TextColumn::make('file_size')
                    ->label('Ukuran')
                    ->formatStateUsing(fn ($state) => $this->formatBytes($state))
                    ->alignCenter(),
                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('restoreFromHistory')
                    ->label('Restore')
                    ->icon('heroicon-o-arrow-up-tray')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Restore Database')
                    ->modalDescription('Semua data saat ini akan diganti dengan data dari backup. Tindakan ini tidak dapat dibatalkan.')
                    ->modalSubmitActionLabel('Ya, Restore')
                    ->action(function (BackupHistory $record) {
                        $filePath = Storage::disk('local')->path("backups/{$record->filename}");

                        if (! file_exists($filePath)) {
                            Notification::make()
                                ->warning()
                                ->title('File backup tidak ditemukan')
                                ->send();

                            return;
                        }

                        try {
                            app(BackupService::class)->restore($filePath);
                            app(AuditService::class)->logBackup('restore', $record->filename);

                            Notification::make()
                                ->success()
                                ->title('Restore berhasil')
                                ->body("Data berhasil direstore dari {$record->filename}")
                                ->send();

                            $this->resetRestore();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Restore gagal')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
            ]);
    }

    public function getBreadcrumbs(): array
    {
        return [
            BackupHistoryResource::getUrl('index') => 'Riwayat Backup',
            'Restore Backup',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali ke Riwayat Backup')
                ->icon('heroicon-o-arrow-left')
                ->url(fn (): string => BackupHistoryResource::getUrl('index'))
                ->color('gray'),
        ];
    }

    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
