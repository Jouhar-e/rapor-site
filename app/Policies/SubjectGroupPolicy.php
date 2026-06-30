<?php

namespace App\Policies;

use App\Models\SubjectGroup;
use App\Models\User;

class SubjectGroupPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('subject-group.view');
    }

    public function view(User $user, SubjectGroup $subjectGroup): bool
    {
        return $user->can('subject-group.view');
    }

    public function create(User $user): bool
    {
        return $user->can('subject-group.create');
    }

    public function update(User $user, SubjectGroup $subjectGroup): bool
    {
        return $user->can('subject-group.edit');
    }

    public function delete(User $user, SubjectGroup $subjectGroup): bool
    {
        return $user->can('subject-group.delete');
    }

    public function restore(User $user, SubjectGroup $subjectGroup): bool
    {
        return false;
    }

    public function forceDelete(User $user, SubjectGroup $subjectGroup): bool
    {
        return false;
    }
}
