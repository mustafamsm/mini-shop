<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Models\Category;
use App\Models\Product;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:view products')->only(['index', 'edit']);
        $this->middleware('permission:create products')->only(['create', 'store']);
        $this->middleware('permission:edit products')->only(['update']);
        $this->middleware('permission:delete products')->only(['destroy']);
    }

    public function index()
    {

        return Inertia::render('Admin/Products/Index', [
            'products' => Product::with('category')->latest()->paginate(15),

        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $categories = Category::latest()->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request)
    {
        $product = Product::create($request->productData());

        return redirect()->route('admin.products.edit', $product)
            ->with('toast', ['type' => 'success', 'message' => 'Product created.']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $product->append('image_urls');

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product->load('variants'),
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreProductRequest $request, Product $product)
    {

        $product->update($request->productData());

        return redirect()->route('admin.products.index', $product)
            ->with('toast', ['type' => 'success', 'message' => 'Product updated.']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Product removed.']);
    }
}
