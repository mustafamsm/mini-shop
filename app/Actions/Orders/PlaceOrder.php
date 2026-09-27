<?php

namespace App\Actions\Orders;

use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PlaceOrder
{
    public function handle(User $user, int $addressId): Order
    {
        $cart = Cart::where('user_id', $user->id)->with('items.productVariant.product')->firstOrFail();

        if ($cart->items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => 'Your cart is empty.']);
        }

        $order = DB::transaction(function () use ($cart, $user, $addressId) {
            $lockedVariants = [];

            // Lock every variant row up front, checking stock against the FRESH, locked value
            foreach ($cart->items as $item) {
                $variant = ProductVariant::where('id', $item->product_variant_id)
                    ->lockForUpdate()
                    ->first();

                if (! $variant || $variant->stock < $item->quantity) {
                    throw ValidationException::withMessages([
                        'cart' => "{$item->productVariant->product->name} doesn't have enough stock.",
                    ]);
                }

                $lockedVariants[$item->id] = $variant;
            }

            $subtotal = $cart->items->sum(fn ($item) => $item->productVariant->price() * $item->quantity);

            $order = Order::create([
                'order_number' => 'ORD-'.strtoupper(uniqid()),
                'user_id' => $user->id,
                'address_id' => $addressId,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'tax' => 0,
                'shipping' => 0,
                'total' => $subtotal,
            ]);

            foreach ($cart->items as $item) {
                $variant = $lockedVariants[$item->id];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_variant_id' => $item->product_variant_id,
                    'product_name' => $item->productVariant->product->name,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->productVariant->price(),
                ]);

                // decrement using the SAME locked row we already validated
                $variant->decrement('stock', $item->quantity);
            }

            $cart->items()->delete();

            return $order;
        });

        event(new OrderPlaced($order->load('items.productVariant.product')));

        return $order;
    }
}
