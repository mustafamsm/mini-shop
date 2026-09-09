<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OrderController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:view orders')->only(['index', 'edit']);
        $this->middleware('permission:create orders')->only(['create', 'store']);
        $this->middleware('permission:edit orders')->only(['updateStatus']);
        $this->middleware('permission:delete orders')->only(['destroy']);
    }
    public function index(Request $request)
    {
        $orders = Order::with('user')
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(15);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only('status'),
        ]);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validted = $request->validate([
            'status' => ['required', 'in:pending,paid,processing,shipped,delivered,cancelled'],        ]);
        $order->update($validted);

        return back()->with('toast',['type'=>'success','message'=>'Order status updated.']);
    }
}
