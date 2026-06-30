<?php

namespace App\Policies;

use App\Models\BackupHistory;
use App\Models\User;

class BackupHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('backup.view');
    }

    public function view(User $user, BackupHistory $backupHistory): bool
    {
        return $user->can('backup.view');
    }

    public function create(User $user): bool
    {
        return $user->can('backup.create');
    }

    public function update(User $user, BackupHistory $backupHistory): bool
    {
        return false;
    }

    public function delete(User $user, BackupHistory $backupHistory): bool
    {
        return false;
    }
}
