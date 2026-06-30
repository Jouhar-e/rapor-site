<?php

namespace App\Policies;

use App\Models\ClassLearner;
use App\Models\User;

class ClassLearnerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('class-learner.view');
    }

    public function view(User $user, ClassLearner $classLearner): bool
    {
        return $user->can('class-learner.view');
    }

    public function create(User $user): bool
    {
        return $user->can('class-learner.create');
    }

    public function update(User $user, ClassLearner $classLearner): bool
    {
        return $user->can('class-learner.edit');
    }

    public function delete(User $user, ClassLearner $classLearner): bool
    {
        return $user->can('class-learner.delete');
    }

    public function restore(User $user, ClassLearner $classLearner): bool
    {
        return false;
    }

    public function forceDelete(User $user, ClassLearner $classLearner): bool
    {
        return false;
    }
}
