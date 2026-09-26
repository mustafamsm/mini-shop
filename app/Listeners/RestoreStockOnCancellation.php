<?php

namespace App\Listeners;

use App\Events\OrderStatusChanged;
use App\Models\ProductVariant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class RestoreStockOnCancellation
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
        if ($event->order->status !== 'cancelled' || $event->oldStatus === 'cancelled') {
            return;
        }

        DB::transaction(function () use ($event) {
            foreach ($event->order->items as $item) {
                $vairant = ProductVariant::where('id', $item->product_variant_id)->lockForUpdate()->first();
                if ($vairant) {
                    $vairant->increment('stock', $item->quantity);
                }
            }
        });
    }
}
