<?php

namespace App\Policies;

use App\Models\Extracurricular;
use App\Models\User;

class ExtracurricularPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('extracurricular.view');
    }

    public function view(User $user, Extracurricular $extracurricular): bool
    {
        return $user->can('extracurricular.view');
    }

    public function create(User $user): bool
    {
        return $user->can('extracurricular.create');
    }

    public function update(User $user, Extracurricular $extracurricular): bool
    {
        return $user->can('extracurricular.edit');
    }

    public function delete(User $user, Extracurricular $extracurricular): bool
    {
        return $user->can('extracurricular.delete');
    }

    public function restore(User $user, Extracurricular $extracurricular): bool
    {
        return false;
    }

    public function forceDelete(User $user, Extracurricular $extracurricular): bool
    {
        return false;
    }
}
