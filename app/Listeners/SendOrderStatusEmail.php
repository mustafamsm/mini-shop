<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Mail\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;

class SendOrderStatusEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderStatusChanged $event): void
    {
        // Only email for statuses the customer actually cares about hearing —
        // not every possible transition (e.g. pending → paid happens instantly, not worth an email)
        if (! in_array($event->order->status, ['shipped', 'delivered', 'cancelled'])) {
            return;
        }
        Mail::to($event->order->user->email)
            ->send(new OrderStatusUpdated($event->order, $event->oldStatus));
    }
}
