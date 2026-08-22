<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;

class ProductVariantController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validated = $request->validate([
            'sku' => ['required', 'string', 'unique:product_variants,sku'],
            'name' => ['nullable', 'string'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->variants()->create($validated);

        return back()->with('success', 'Variant added.');
    }

    public function update(Request $request, ProductVariant $variant)
    {
        $request->validate(['stock' => ['required', 'integer', 'min:0']]);
        $variant->update($request->only('stock'));

        return back();
    }

    public function destroy(ProductVariant $variant)
    {
        $variant->delete();

        return back();
    }
}
