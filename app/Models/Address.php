<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;


#[Fillable(['user_id', 'label', 'line1', 'line2', 'city', 'state', 'postal_code', 'country'])]
class Address extends Model
{



    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
