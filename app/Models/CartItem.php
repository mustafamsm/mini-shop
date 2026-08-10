<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['cart_id', 'product_variant_id', 'quantity'])]
class CartItem extends Model
{
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
