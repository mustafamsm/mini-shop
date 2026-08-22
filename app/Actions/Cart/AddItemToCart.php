<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\CartItem;

class AddItemToCart
{
    public function handle(Cart $cart, int $variantId, int $quantity): CartItem
    {
        $item = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variantId)
            ->first();

        if ($item) {
            $item->increment('quantity', $quantity);

            return $item;
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ]);

    }
}
