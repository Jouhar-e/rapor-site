<?php

namespace App\Policies;

use App\Models\Program;
use App\Models\User;

class ProgramPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('program.view');
    }

    public function view(User $user, Program $program): bool
    {
        return $user->can('program.view');
    }

    public function create(User $user): bool
    {
        return $user->can('program.create');
    }

    public function update(User $user, Program $program): bool
    {
        return $user->can('program.edit');
    }

    public function delete(User $user, Program $program): bool
    {
        return $user->can('program.delete');
    }

    public function restore(User $user, Program $program): bool
    {
        return false;
    }

    public function forceDelete(User $user, Program $program): bool
    {
        return false;
    }
}
