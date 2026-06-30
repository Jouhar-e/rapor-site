<?php

namespace App\Policies;

use App\Models\Classes;
use App\Models\User;

class ClassesPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('class.view');
    }

    public function view(User $user, Classes $classes): bool
    {
        return $user->can('class.view');
    }

    public function create(User $user): bool
    {
        return $user->can('class.create');
    }

    public function update(User $user, Classes $classes): bool
    {
        return $user->can('class.edit');
    }

    public function delete(User $user, Classes $classes): bool
    {
        return $user->can('class.delete');
    }

    public function restore(User $user, Classes $classes): bool
    {
        return false;
    }

    public function forceDelete(User $user, Classes $classes): bool
    {
        return false;
    }
}
