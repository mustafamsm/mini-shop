<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use Illuminate\Validation\ValidationException;

class AddItemToCart
{
    public function handle(Cart $cart, int $variantId, int $quantity): CartItem
    {
        if ($quantity <= 0) {
            throw ValidationException::withMessages([
                'quantity' => 'Quantity must be greater than zero.',
            ]);
        }

        $variant = ProductVariant::find($variantId);

        if (! $variant) {
            throw ValidationException::withMessages([
                'product' => 'Selected product variant was not found.',
            ]);
        }

        $existingItem = CartItem::where('cart_id', $cart->id)
            ->where('product_variant_id', $variantId)
            ->first();

        $newQuantity = $existingItem ? $existingItem->quantity + $quantity : $quantity;

        if ($newQuantity > $variant->stock) {
            throw ValidationException::withMessages([
                'cart' => "{$variant->product->name} doesn't have enough stock.",
            ]);
        }

        if ($existingItem) {
            $existingItem->increment('quantity', $quantity);

            return $existingItem->fresh();
        }

        return CartItem::create([
            'cart_id' => $cart->id,
            'product_variant_id' => $variantId,
            'quantity' => $quantity,
        ]);
    }
}
