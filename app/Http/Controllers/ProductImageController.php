<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductImageController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ]);

        $product->addMediaFromRequest('image')->toMediaCollection('images');

        return back()->with('toast', ['type' => 'success', 'message' => 'Image uploaded.']);
    }

    public function destroy(Product $product, string $mediaId)
    {
        $product->media()->findOrFail($mediaId)->delete();

        return back()->with('toast', ['type' => 'success', 'message' => 'Image removed.']);
    }
}
