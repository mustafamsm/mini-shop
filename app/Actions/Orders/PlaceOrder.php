<?php

namespace App\Actions\Orders;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function handle(User $user, int $addressId): Order
    {
        $cart = Cart::where('user_id', $user->id)
            ->with('items.productVariant.product')
            ->fistOrFail();


        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }


        foreach ($cart->items as $item) {
            if ($item->productVariant->stock < $item->quantity) {
                throw ValidationException::withMessages([
                    'cart' => "{$$item->productVariant->product->name} doesn't have enough stock."
                ]);
            }
        }
        return DB::transaction(function () use ($cart, $user, $addressId) {
            $subtotal = $cart->items->sum(fn($item) => $item->productVariant->price() * $item->quantity);

            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'user_id' => $user->id,
                'address_id' => $addressId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => 0,
                'shipping' => 0,
                'total' => $subtotal,

            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->productVariant->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->productVariant->price(),

                ]);
                $item->productVariant->decrement('stock', $item->quantity);
            }
            $cart->items()->delete();
            return $order;
        });
    }
}
