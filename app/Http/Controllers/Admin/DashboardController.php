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
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_revenue' => Order::where('status', '!=', 'cancelled')->sum('total'),
                'orders_today' => Order::whereDate('created_at', today())->count(),
                'low_stock_variants' => Product::whereHas('variants', fn ($q) => $q->where('stock', '<', 5))->count(),
            ],
        ]);
    }
}
