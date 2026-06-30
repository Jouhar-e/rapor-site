<?php

namespace App\Policies;

use App\Models\Notification;
use App\Models\User;

class NotificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('notification.view');
    }

    public function view(User $user, Notification $notification): bool
    {
        return $user->can('notification.view');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function update(User $user, Notification $notification): bool
    {
        return false;
    }

    public function delete(User $user, Notification $notification): bool
    {
        return false;
    }

    public function restore(User $user, Notification $notification): bool
    {
        return false;
    }

    public function forceDelete(User $user, Notification $notification): bool
    {
        return false;
    }
}
