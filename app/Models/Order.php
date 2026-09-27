<?php

namespace App\Models;

use App\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'order_number',
    'user_id',
    'address_id',
    'status',
    'subtotal',
    'tax',
    'shipping',
    'total',
    'stripe_payment_intent_id',
])]
class Order extends Model
{

protected function casts():array
{
    return ['status' => OrderStatus::class];
}
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
