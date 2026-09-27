<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;

class ProductRepository
{
    public function activeCatalog(?string $categorySlug = null, ?string $search = null): LengthAwarePaginator
    {
        return $this->baseQuery()
            ->when(
                $categorySlug,
                fn(Builder $q) =>
                $q->whereHas('category', fn($query) => $query->where('slug', $categorySlug))
            )
            ->when(
                $search,
                fn(Builder $q) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->latest()
            ->paginate(12)
            ->withQueryString();
    }
    public function lowStockCount(int $threshold): int
    {
        return $this->baseQuery()
            ->whereHas('variants', fn($query) => $query->where('stock', '<', $threshold))
            ->count();
    }
    private function baseQuery():Builder{
        return \App\Models\Product::query()
        ->with('variants')
        ->where('is_active', true);
    }
}
