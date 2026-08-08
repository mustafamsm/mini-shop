<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['product_id', 'sku', 'name', 'price_override', 'stock'])]
class ProductVariant extends Model
{



    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
