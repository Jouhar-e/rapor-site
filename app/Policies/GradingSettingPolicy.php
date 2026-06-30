<?php

namespace App\Policies;

use App\Models\GradingSetting;
use App\Models\User;

class GradingSettingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('grading-setting.view');
    }

    public function view(User $user, GradingSetting $gradingSetting): bool
    {
        return $user->can('grading-setting.view');
    }

    public function update(User $user, GradingSetting $gradingSetting): bool
    {
        return $user->can('grading-setting.edit');
    }

    public function create(User $user): bool
    {
        return false;
    }

    public function delete(User $user, GradingSetting $gradingSetting): bool
    {
        return false;
    }

    public function restore(User $user, GradingSetting $gradingSetting): bool
    {
        return false;
    }

    public function forceDelete(User $user, GradingSetting $gradingSetting): bool
    {
        return false;
    }
}
