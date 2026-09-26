<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        return response()->json([
            'notifications' => $request->user()->notifications()->latest()->take(10)->get(),
            'unread_count' => $request->user()->unreadNotifications()->count(),

        ]);
    }


    public function markAsRead(Request $request, string $notificationId)
    {
        $request->user()->notifications()->where('id', $notificationId)->first()?->markAsRead();

        return back();
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications()->update(['read_at' => now()]);

        return back();
    }
}
