<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class NotificationService
{
    public function getAdminNotifications(): Collection
    {
        return Notification::where('notifiable_type', User::class)
            ->whereHas('notifiable', fn ($q) => $q->whereHas('roles', fn ($q) => $q->where('name', 'admin')))
            ->latest()
            ->take(20)
            ->get();
    }

    public function getTutorNotifications(User $user): Collection
    {
        return Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', $user->id)
            ->latest()
            ->take(20)
            ->get();
    }

    public function createNotification(string $type, $notifiable, array $data): Notification
    {
        return Notification::create([
            'type' => $type,
            'notifiable_id' => $notifiable->getKey(),
            'notifiable_type' => $notifiable->getMorphClass(),
            'data' => json_encode($data),
            'read_at' => null,
        ]);
    }

    public function markAsRead(Notification $notification): void
    {
        $notification->update(['read_at' => now()]);
    }
}
