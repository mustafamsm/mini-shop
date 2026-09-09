<?php

namespace App\Http\Controllers;

use App\Actions\Orders\PlaceOrder;
use App\Http\Requests\CheckoutRequest;
use App\Models\Cart;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CheckoutController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load('items.productVariant.product');

        return Inertia::render('Checkout/Index', [
            'cart' => $cart,
            'addresses' => $request->user()->addresses,
        ]);
    }

    public function store(CheckoutRequest $request, PlaceOrder $action)
    {
        $order = $action->handle($request->user(), $request->validated('address_id'));

        return redirect()->route('orders.show', $order)->with('toast', ['type' => 'success', 'message' => 'Order placed.']);
    }
}
