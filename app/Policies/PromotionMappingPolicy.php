<?php

namespace App\Policies;

use App\Models\PromotionMapping;
use App\Models\User;

class PromotionMappingPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('promotion-mapping.view');
    }

    public function view(User $user, PromotionMapping $promotionMapping): bool
    {
        return $user->can('promotion-mapping.view');
    }

    public function create(User $user): bool
    {
        return $user->can('promotion-mapping.create');
    }

    public function update(User $user, PromotionMapping $promotionMapping): bool
    {
        return $user->can('promotion-mapping.edit');
    }

    public function delete(User $user, PromotionMapping $promotionMapping): bool
    {
        return $user->can('promotion-mapping.delete');
    }

    public function restore(User $user, PromotionMapping $promotionMapping): bool
    {
        return false;
    }

    public function forceDelete(User $user, PromotionMapping $promotionMapping): bool
    {
        return false;
    }
}
