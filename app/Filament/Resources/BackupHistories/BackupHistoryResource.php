<?php

namespace App\Filament\Resources\BackupHistories;

use App\Filament\Resources\BackupHistories\Pages\ManageBackupHistories;
use App\Models\BackupHistory;
use App\Services\AuditService;
use App\Services\BackupService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;
use UnitEnum;

class BackupHistoryResource extends Resource
{
    protected static ?string $model = BackupHistory::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static string|UnitEnum|null $navigationGroup = 'Sistem';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'filename';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('admin');
    }

    public static function getModelLabel(): string
    {
        return 'Riwayat Backup';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Riwayat Backup';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('filename')
                    ->required(),
                TextInput::make('file_size')
                    ->numeric()
                    ->default(null),
                TextInput::make('type')
                    ->default(null),
                TextInput::make('status')
                    ->default('pending'),
                TextInput::make('created_by')
                    ->numeric()
                    ->default(null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('filename')
            ->columns([
                TextColumn::make('filename')
                    ->searchable(),
                TextColumn::make('file_size')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'running' => 'warning',
                        'failed' => 'danger',
                        'pending' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('created_by')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('started_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('completed_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->action(function (BackupHistory $record) {
                        if (! Storage::disk('local')->exists("backups/{$record->filename}")) {
                            Notification::make()
                                ->warning()
                                ->title('File tidak ditemukan')
                                ->send();

                            return;
                        }

                        return Storage::disk('local')->download("backups/{$record->filename}");
                    }),
                DeleteAction::make(),
            ])
            ->emptyStateHeading('Belum ada backup')
            ->emptyStateDescription('Belum ada riwayat backup sistem.')
            ->emptyStateIcon('heroicon-o-circle-stack')
            ->toolbarActions([
                Action::make('backupNow')
                    ->label('Backup Sekarang')
                    ->icon('heroicon-o-play')
                    ->color('success')
                    ->action(function () {
                        try {
                            $filename = app(BackupService::class)->backupDatabase();
                            app(AuditService::class)->logBackup('create', $filename);

                            Notification::make()
                                ->success()
                                ->title('Backup berhasil')
                                ->body("File: {$filename}")
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->danger()
                                ->title('Backup gagal')
                                ->body($e->getMessage())
                                ->send();
                        }
                    }),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageBackupHistories::route('/'),
        ];
    }
}
