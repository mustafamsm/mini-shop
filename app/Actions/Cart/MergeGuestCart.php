<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\User;

class MergeGuestCart
{
    public function handle(User $user, string $sessionId): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->with('items')->first();

        if (! $guestCart || $guestCart->items->isEmpty()) {
            return;
        }

        $userCart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($guestCart->items as $guestItem) {
            $existingItem = $userCart->items()
                ->where('product_variant_id', $guestItem->product_variant_id)
                ->first();

            if ($existingItem) {
                $existingItem->quantity += $guestItem->quantity;
                $existingItem->save();

                continue;
            }

            $userCart->items()->create([
                'product_variant_id' => $guestItem->product_variant_id,
                'quantity' => $guestItem->quantity,
            ]);
        }

        $guestCart->delete();
    }
}
