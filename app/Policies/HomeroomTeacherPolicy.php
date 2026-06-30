<?php

namespace App\Policies;

use App\Models\HomeroomTeacher;
use App\Models\User;

class HomeroomTeacherPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('homeroom-teacher.view');
    }

    public function view(User $user, HomeroomTeacher $homeroomTeacher): bool
    {
        return $user->can('homeroom-teacher.view');
    }

    public function create(User $user): bool
    {
        return $user->can('homeroom-teacher.create');
    }

    public function update(User $user, HomeroomTeacher $homeroomTeacher): bool
    {
        return $user->can('homeroom-teacher.edit');
    }

    public function delete(User $user, HomeroomTeacher $homeroomTeacher): bool
    {
        return $user->can('homeroom-teacher.delete');
    }

    public function restore(User $user, HomeroomTeacher $homeroomTeacher): bool
    {
        return false;
    }

    public function forceDelete(User $user, HomeroomTeacher $homeroomTeacher): bool
    {
        return false;
    }
}
