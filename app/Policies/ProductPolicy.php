<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function update(User $user, Product $product): bool
    {
        // Harus admin DAN harus datanya sendiri
        return $user->role === 'admin' && $user->id === $product->user_id;
    }

    public function delete(User $user, Product $product): bool
    {
        // Harus admin DAN harus datanya sendiri
        return $user->role === 'admin' && $user->id === $product->user_id;
    }
}