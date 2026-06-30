<?php

namespace App\Policies;

use App\Models\Grade;
use App\Models\User;

class GradePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('grade.view');
    }

    public function view(User $user, Grade $grade): bool
    {
        return $user->can('grade.view');
    }

    public function create(User $user): bool
    {
        return $user->can('grade.create');
    }

    public function update(User $user, Grade $grade): bool
    {
        return $user->can('grade.edit');
    }

    public function delete(User $user, Grade $grade): bool
    {
        return $user->can('grade.delete');
    }

    public function publish(User $user): bool
    {
        return $user->can('grade.publish');
    }

    public function lock(User $user): bool
    {
        return $user->can('grade.lock');
    }

    public function restore(User $user, Grade $grade): bool
    {
        return false;
    }

    public function forceDelete(User $user, Grade $grade): bool
    {
        return false;
    }
}
