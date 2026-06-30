<?php

namespace App\Policies;

use App\Models\Phase;
use App\Models\User;

class PhasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('phase.view');
    }

    public function view(User $user, Phase $phase): bool
    {
        return $user->can('phase.view');
    }

    public function create(User $user): bool
    {
        return $user->can('phase.create');
    }

    public function update(User $user, Phase $phase): bool
    {
        return $user->can('phase.edit');
    }

    public function delete(User $user, Phase $phase): bool
    {
        return $user->can('phase.delete');
    }

    public function restore(User $user, Phase $phase): bool
    {
        return false;
    }

    public function forceDelete(User $user, Phase $phase): bool
    {
        return false;
    }
}
