<?php

namespace App\Policies;

use App\Models\Learner;
use App\Models\User;

class LearnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('learner.view');
    }

    public function view(User $user, Learner $learner): bool
    {
        return $user->can('learner.view');
    }

    public function create(User $user): bool
    {
        return $user->can('learner.create');
    }

    public function update(User $user, Learner $learner): bool
    {
        return $user->can('learner.edit');
    }

    public function delete(User $user, Learner $learner): bool
    {
        return $user->can('learner.delete');
    }

    public function restore(User $user, Learner $learner): bool
    {
        return false;
    }

    public function forceDelete(User $user, Learner $learner): bool
    {
        return false;
    }
}
