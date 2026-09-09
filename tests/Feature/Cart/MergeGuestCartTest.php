<?php

namespace Tests\Feature\Cart;

use App\Listeners\MergeGuestCartOnLogin;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Auth\Events\Login;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class MergeGuestCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cart_is_merged_when_user_logs_in(): void
    {
        $user = User::factory()->create();
        $variant = ProductVariant::factory()->create();
        $guestSessionId = 'guest_session_1234567890';

        $guestCart = Cart::create(['session_id' => $guestSessionId]);
        CartItem::create([
            'cart_id' => $guestCart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $request = Request::create('/login', 'POST');
        $request->setLaravelSession($this->app['session']->driver());
        $request->session()->setId('new_session_after_login');
        $request->session()->put('guest_cart_session_id', $guestSessionId);

        $listener = new MergeGuestCartOnLogin($request);
        $listener->handle(new Login('web', $user, false));

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseMissing('carts', [
            'id' => $guestCart->id,
        ]);
    }

    public function test_guest_cart_is_merged_when_user_registers(): void
    {
        $variant = ProductVariant::factory()->create();
        $guestSessionId = 'guest_register_session_123456';

        $guestCart = Cart::create(['session_id' => $guestSessionId]);
        CartItem::create([
            'cart_id' => $guestCart->id,
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $this->withSession([
            'guest_cart_session_id' => $guestSessionId,
        ])->post(route('register.store'), [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $user = User::where('email', 'newuser@example.com')->firstOrFail();

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'session_id' => null,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'product_variant_id' => $variant->id,
            'quantity' => 3,
        ]);

        $this->assertDatabaseMissing('carts', [
            'id' => $guestCart->id,
        ]);
    }
}
