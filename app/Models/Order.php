<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;



#[Fillable([
    'order_number', 'user_id', 'address_id', 'status',
    'subtotal', 'tax', 'shipping', 'total', 'stripe_payment_intent_id',
])]
class Order extends Model
{
    //
}
