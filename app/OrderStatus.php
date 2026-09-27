<?php

namespace App;

enum OrderStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Processing = 'processing';
    case Shipped = 'shipped';
    case Delivered = 'delivered';
    case Cancelled = 'cancelled';

    public function isFinal():bool{
        return in_array($this,[self::Delivered, self::Cancelled]);
    }
    public function label():string{
        return match($this){
            self::Pending=>'Pending',
            self::Paid=>'Paid',
            self::Processing=>'Processing',
            self::Shipped=>'Shipped',
            self::Delivered=>'Delivered',
            self::Cancelled=>'Cancelled'
        };
    }

}
