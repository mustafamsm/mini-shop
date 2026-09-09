<?php

namespace App\Http\Controllers;

use App\Actions\Cart\AddItemToCart;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CartController extends Controller
{
    public function index(Request $request)
    {

        $cart = $this->currentCart($request);
        $cart->load('items.productVariant.product');
        $cart->items->each(
            fn ($item) => $item->productVariant->product->append('image_urls')
        );

        return Inertia::render('Cart/Index', ['cart' => $cart]);
    }

    public function store(Request $request, AddItemToCart $action)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $action->handle($this->currentCart($request), $validated['product_variant_id'], $validated['quantity']);

        return back()->with('toast', ['type' => 'success', 'message' => 'Added to cart.']);
    }

    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();

        return back();
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],

        ]);
        $cartItem->update($request->only('quantity'));

        return back();
    }

    private function currentCart(Request $request): Cart
    {
        return Cart::resolveForRequest($request);
    }
}
