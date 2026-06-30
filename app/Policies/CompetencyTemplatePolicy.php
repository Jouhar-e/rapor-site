<?php

namespace App\Policies;

use App\Models\CompetencyTemplate;
use App\Models\User;

class CompetencyTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('competency-template.view');
    }

    public function view(User $user, CompetencyTemplate $competencyTemplate): bool
    {
        return $user->can('competency-template.view');
    }

    public function create(User $user): bool
    {
        return $user->can('competency-template.create');
    }

    public function update(User $user, CompetencyTemplate $competencyTemplate): bool
    {
        return $user->can('competency-template.edit');
    }

    public function delete(User $user, CompetencyTemplate $competencyTemplate): bool
    {
        return $user->can('competency-template.delete');
    }

    public function restore(User $user, CompetencyTemplate $competencyTemplate): bool
    {
        return false;
    }

    public function forceDelete(User $user, CompetencyTemplate $competencyTemplate): bool
    {
        return false;
    }
}
