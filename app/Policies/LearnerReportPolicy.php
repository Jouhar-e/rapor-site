<?php

namespace App\Policies;

use App\Models\LearnerReport;
use App\Models\User;

class LearnerReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('learner-report.view');
    }

    public function view(User $user, LearnerReport $learnerReport): bool
    {
        return $user->can('learner-report.view');
    }

    public function create(User $user): bool
    {
        return $user->can('learner-report.create');
    }

    public function update(User $user, LearnerReport $learnerReport): bool
    {
        return $user->can('learner-report.edit');
    }

    public function delete(User $user, LearnerReport $learnerReport): bool
    {
        return $user->can('learner-report.delete');
    }

    public function restore(User $user, LearnerReport $learnerReport): bool
    {
        return false;
    }

    public function forceDelete(User $user, LearnerReport $learnerReport): bool
    {
        return false;
    }
}
