<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function __construct(private ProductRepository $productRepository) {}

    public function index(Request $request)
    {

        $products = $this->productRepository->activeCatalog(
            categorySlug: $request->category,
            search: $request->search
        );

        $products->getCollection()->transform(fn ($p) => tap($p)->setAttribute('image_urls', $p->image_urls));

        return Inertia::render('Shop/index', [
            'products' => $products,
            'categories' => Category::whereNull('parent_id')->get(),
            'search' => $request->search,
            'filters' => $request->only('category'),
        ]);

    }

    public function show(Product $product)
    {
        $product->load(['variants', 'category']);

        return Inertia::render('Shop/Show', ['product' => $product->append('image_urls')]);
    }
}
