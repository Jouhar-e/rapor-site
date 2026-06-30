<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;

class SubjectPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('subject.view');
    }

    public function view(User $user, Subject $subject): bool
    {
        return $user->can('subject.view');
    }

    public function create(User $user): bool
    {
        return $user->can('subject.create');
    }

    public function update(User $user, Subject $subject): bool
    {
        return $user->can('subject.edit');
    }

    public function delete(User $user, Subject $subject): bool
    {
        return $user->can('subject.delete');
    }

    public function restore(User $user, Subject $subject): bool
    {
        return false;
    }

    public function forceDelete(User $user, Subject $subject): bool
    {
        return false;
    }
}
