<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        if ($request->user()->hasRole('customer')) {
            $orders = $request->user()->orders()->latest()->get();

            return Inertia::render('Dashboard', [
                'stats' => [
                    'orders_count' => $orders->count(),
                    'pending_orders' => $orders->whereIn('status', ['pending', 'processing'])->count(),
                    'total_spent' => $orders->where('status', '!=', 'cancelled')->sum('total'),
                ],
                'recentOrders' => $orders->take(5)->map(fn ($order) => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'total' => $order->total,
                    'created_at' => $order->created_at?->toDateString(),
                ])->values(),
            ]);
        }

        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'low_stock_variants' => Product::whereHas('variants', fn ($q) => $q->where('stock', '<', 5))->count(),
            ],
        ]);
    }
}
