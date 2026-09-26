<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Models\User;

class NotifyAdminOfNewOrder
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
    public function handle(OrderPlaced $event): void
    {
        $admins = User::role(['admin', 'staff'])
            ->get()
            ->unique('id');

        foreach ($admins as $user) {
            $user->notify(new \App\Notifications\NewOrderNotification($event->order));
        }
    }
}
