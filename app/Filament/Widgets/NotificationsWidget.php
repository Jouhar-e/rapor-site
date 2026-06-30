<?php

namespace App\Filament\Widgets;

use App\Models\Notification;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Support\Facades\Auth;

class NotificationsWidget extends TableWidget
{
    protected static ?string $heading = 'Notifikasi Terbaru';

    public function getColumnSpan(): int|string|array
    {
        return 'full';
    }

    public static function canView(): bool
    {
        return Auth::user()?->hasRole('admin') ?? false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Notification::where('notifiable_type', Auth::user()?->getMorphClass())
                    ->where('notifiable_id', Auth::id())
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'warning' => 'warning',
                        'info' => 'info',
                        'success' => 'success',
                        'error' => 'danger',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'warning' => 'Peringatan',
                        'info' => 'Informasi',
                        'success' => 'Berhasil',
                        'error' => 'Gagal',
                        default => $state,
                    }),
                TextColumn::make('data.message')
                    ->label('Pesan')
                    ->limit(50)
                    ->wrap(),
                TextColumn::make('created_at')
                    ->label('Waktu')
                    ->since(),
                TextColumn::make('read_at')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($state) => $state ? 'gray' : 'primary')
                    ->formatStateUsing(fn ($state) => $state ? 'Dibaca' : 'Baru'),
            ])
            ->actions([
                Action::make('markAsRead')
                    ->label('Tandai Dibaca')
                    ->icon('heroicon-o-check')
                    ->action(fn (Notification $record) => $record->update(['read_at' => now()]))
                    ->visible(fn (Notification $record) => $record->read_at === null),
            ])
            ->paginated(false)
            ->emptyStateIcon('heroicon-o-bell')
            ->emptyStateHeading('Tidak ada notifikasi')
            ->emptyStateDescription('Notifikasi baru akan muncul di sini');
    }
}
