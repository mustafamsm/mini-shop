<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;



#[Fillable(['category_id', 'name', 'slug', 'description', 'base_price', 'is_active'])]
class Product extends Model
{



    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
    public function images()
    {
        return $this->hasMany(ProductImage::class);
    }
    public function reviews()
    {
        return $this->HasMany(Review::class);
    }
}
