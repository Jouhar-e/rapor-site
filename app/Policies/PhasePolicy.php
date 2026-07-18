<?php

namespace App\Policies;

use App\Models\User;

class PhasePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('master-data.view');
    }

    public function view(User $user): bool
    {
        return $user->can('master-data.view');
    }

    public function create(User $user): bool
    {
        return $user->can('master-data.create');
    }

    public function update(User $user): bool
    {
        return $user->can('master-data.update');
    }

    public function delete(User $user): bool
    {
        return $user->can('master-data.delete');
    }
}
