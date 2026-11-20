<?php

namespace App\Policies;

use App\Models\ProductVariant;
use App\Models\User;

class ProductVariantPolicy
{
    public function restock(User $user, ProductVariant $variant): bool
    {
        return $user->isUmkm()
            && $user->isEnabled()
            && ! $user->trashed()
            && $variant->product->business->user_id === $user->id;
    }

    public function sell(User $user, ProductVariant $variant): bool
    {
        return $this->restock($user, $variant)
            && $user->hasVerifiedEmail() && ! $user->must_change_password
            && $variant->archived_at === null && $variant->product->archived_at === null;
    }
}
