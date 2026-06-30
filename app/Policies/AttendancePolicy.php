<?php

namespace App\Policies;

use App\Models\Attendance;
use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('attendance.view');
    }

    public function view(User $user, Attendance $attendance): bool
    {
        return $user->can('attendance.view');
    }

    public function create(User $user): bool
    {
        return $user->can('attendance.create');
    }

    public function update(User $user, Attendance $attendance): bool
    {
        return $user->can('attendance.edit');
    }

    public function delete(User $user, Attendance $attendance): bool
    {
        return $user->can('attendance.delete');
    }

    public function restore(User $user, Attendance $attendance): bool
    {
        return false;
    }

    public function forceDelete(User $user, Attendance $attendance): bool
    {
        return false;
    }
}
