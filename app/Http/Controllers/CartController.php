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
        $cart->load('items.productVariant.product.images');
        return Inertia::render('cart/index', ['cart' => $cart]);
    }

    public function store(Request $request, AddItemToCart $action)
    {
        $validated = $request->validate([
            'product_variant_id' => ['required', 'exists:product_variants,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ]);
        $action->handle($this->currentCart($request), $validated['product_variants'], $validated['quantity']);
        return back()->with('success', 'Added to cart.');
    }
    public function destroy(CartItem $cartItem)
    {
        $cartItem->delete();
        return back();
    }


    private function currentCart(Request $request): Cart
    {
        return $request->user()
            ? Cart::firstOrCreate(['user_id' => $request->user()->id])
            : Cart::firstOrCreate(['session_id' => $request->session()->getId()]);
    }
}
