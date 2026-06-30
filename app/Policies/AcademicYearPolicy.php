<?php

namespace App\Policies;

use App\Models\AcademicYear;
use App\Models\User;

class AcademicYearPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('academic-year.view');
    }

    public function view(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('academic-year.view');
    }

    public function create(User $user): bool
    {
        return $user->can('academic-year.create');
    }

    public function update(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('academic-year.edit');
    }

    public function delete(User $user, AcademicYear $academicYear): bool
    {
        return $user->can('academic-year.delete');
    }

    public function restore(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }

    public function forceDelete(User $user, AcademicYear $academicYear): bool
    {
        return false;
    }
}
