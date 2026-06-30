<?php

namespace App\Policies;

use App\Models\SchoolProfile;
use App\Models\User;

class SchoolProfilePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('school-profile.view');
    }

    public function view(User $user, SchoolProfile $schoolProfile): bool
    {
        return $user->can('school-profile.view');
    }

    public function update(User $user, SchoolProfile $schoolProfile): bool
    {
        return $user->can('school-profile.edit');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, SchoolProfile $schoolProfile): bool
    {
        return false;
    }

    public function restore(User $user, SchoolProfile $schoolProfile): bool
    {
        return false;
    }

    public function forceDelete(User $user, SchoolProfile $schoolProfile): bool
    {
        return false;
    }
}
