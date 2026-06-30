<?php

namespace App\Policies;

use App\Models\Tutor;
use App\Models\User;

class TutorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('tutor.view');
    }

    public function view(User $user, Tutor $tutor): bool
    {
        return $user->can('tutor.view');
    }

    public function create(User $user): bool
    {
        return $user->can('tutor.create');
    }

    public function update(User $user, Tutor $tutor): bool
    {
        return $user->can('tutor.edit');
    }

    public function delete(User $user, Tutor $tutor): bool
    {
        return $user->can('tutor.delete');
    }

    public function restore(User $user, Tutor $tutor): bool
    {
        return false;
    }

    public function forceDelete(User $user, Tutor $tutor): bool
    {
        return false;
    }
}
