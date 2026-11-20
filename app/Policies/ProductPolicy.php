<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isUmkm() && $user->isEnabled() && ! $user->trashed()
            && $user->hasVerifiedEmail() && ! $user->must_change_password;
    }

    public function create(User $user): bool
    {
        return $this->viewAny($user) && $user->business()->exists();
    }

    public function update(User $user, Product $product): Response
    {
        return $this->viewAny($user) && $product->business->user_id === $user->id
            ? Response::allow() : Response::denyAsNotFound();
    }

    public function archive(User $user, Product $product): Response
    {
        return $this->update($user, $product);
    }
}
