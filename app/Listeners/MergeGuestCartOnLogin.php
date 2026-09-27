<?php

namespace App\Listeners;

use App\Actions\Cart\MergeGuestCart;
use Illuminate\Http\Request;

class MergeGuestCartOnLogin
{
    /**
     * Create the event listener.
     */
    public function __construct(private Request $request)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(mixed $event): void
    {
        $sessionId = $this->request->session()->get('guest_cart_session_id')
            ?? $this->request->session()->getId();

        app(MergeGuestCart::class)->handle($event->user, $sessionId);

        $this->request->session()->forget('guest_cart_session_id');
    }
}
