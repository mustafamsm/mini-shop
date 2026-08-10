<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id', 'sku', 'name', 'price_override', 'stock'])]
class ProductVariant extends Model
{
    protected function casts(): array
    {
        return [
            'price_override' => 'decimal:2',
        ];
    }
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
    public function price(): float
    {
        return $this->price_override ?? $this->product->base_price;
    }
}
