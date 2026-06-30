<?php

namespace App\Policies;

use App\Models\Semester;
use App\Models\User;

class SemesterPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('semester.view');
    }

    public function view(User $user, Semester $semester): bool
    {
        return $user->can('semester.view');
    }

    public function create(User $user): bool
    {
        return $user->can('semester.create');
    }

    public function update(User $user, Semester $semester): bool
    {
        return $user->can('semester.edit');
    }

    public function delete(User $user, Semester $semester): bool
    {
        return $user->can('semester.delete');
    }

    public function restore(User $user, Semester $semester): bool
    {
        return false;
    }

    public function forceDelete(User $user, Semester $semester): bool
    {
        return false;
    }
}
