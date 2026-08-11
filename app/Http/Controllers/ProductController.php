<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index(Request $request)
    {

        $products = Product::query()
            ->with(['images', 'variants'])
            ->where('is_active', true)
            ->when($request->category, fn ($q, $slug) => $q->wherHas('category', fn ($q2) => $q->where('slug', $slug))
            )->when($request->search, fn ($q, $search) => $q->where('name', 'like', "%{$search}%")
            )->latest()
            ->paginate(12)
            ->withQueryString();

        return Inertia::render('Shop/index', [
            'products' => $products,
            'categories' => Category::whereNull('parent_id')->get(),
            'search' => $request->search,
            'filters'=>$request->only('category')
        ]);


    }

    public function show(Product $product){
        $product->load(['images','variants','cateogy']);
        return  Inertia::render('shop/show',['product'=>$product]);
    }
}
