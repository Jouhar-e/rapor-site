<?php

namespace App\Policies;

use App\Models\LearnerExtracurricular;
use App\Models\User;

class LearnerExtracurricularPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('learner-extracurricular.view');
    }

    public function view(User $user, LearnerExtracurricular $learnerExtracurricular): bool
    {
        return $user->can('learner-extracurricular.view');
    }

    public function create(User $user): bool
    {
        return $user->can('learner-extracurricular.create');
    }

    public function update(User $user, LearnerExtracurricular $learnerExtracurricular): bool
    {
        return $user->can('learner-extracurricular.edit');
    }

    public function delete(User $user, LearnerExtracurricular $learnerExtracurricular): bool
    {
        return $user->can('learner-extracurricular.delete');
    }

    public function restore(User $user, LearnerExtracurricular $learnerExtracurricular): bool
    {
        return false;
    }

    public function forceDelete(User $user, LearnerExtracurricular $learnerExtracurricular): bool
    {
        return false;
    }
}
