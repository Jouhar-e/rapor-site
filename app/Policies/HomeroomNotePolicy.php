<?php

namespace App\Policies;

use App\Models\HomeroomNote;
use App\Models\User;

class HomeroomNotePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('homeroom-note.view');
    }

    public function view(User $user, HomeroomNote $homeroomNote): bool
    {
        return $user->can('homeroom-note.view');
    }

    public function create(User $user): bool
    {
        return $user->can('homeroom-note.create');
    }

    public function update(User $user, HomeroomNote $homeroomNote): bool
    {
        return $user->can('homeroom-note.edit');
    }

    public function delete(User $user, HomeroomNote $homeroomNote): bool
    {
        return $user->can('homeroom-note.delete');
    }

    public function restore(User $user, HomeroomNote $homeroomNote): bool
    {
        return false;
    }

    public function forceDelete(User $user, HomeroomNote $homeroomNote): bool
    {
        return false;
    }
}
