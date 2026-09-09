<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user?->hasAnyRole(['admin', 'staff'])) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('dashboard');
    }
}
