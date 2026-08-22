<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('orders/index', [
            'orders' => $request->user()->orders()->latest()->paginate(10),
        ]);
    }

    public function show(Order $order)
    {

        abort_unless($order->user_id === auth()->user()->id, 403);
        $order->load('items');

        return Inertia::render('Orders/Show', [
            'order' => $order,
        ]);
    }
}
