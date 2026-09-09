<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

#[Fillable(['user_id', 'session_id'])]
class Cart extends Model
{
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public static function forUser(User $user): self
    {
        return self::firstOrCreate(['user_id' => $user->id]);
    }

    public static function forSession(string $sessionId): self
    {
        return self::firstOrCreate(['session_id' => $sessionId]);
    }

    public static function resolveForRequest(Request $request): self
    {
        if ($request->user()) {
            return self::forUser($request->user());
        }

        $sessionId = $request->session()->getId();
        $request->session()->put('guest_cart_session_id', $sessionId);

        return self::forSession($sessionId);
    }
}
