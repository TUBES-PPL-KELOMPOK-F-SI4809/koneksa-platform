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
}
