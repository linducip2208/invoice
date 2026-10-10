<?php

namespace App\Http\Controllers;

use App\Models\AppNotification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = AppNotification::latest()->paginate(30);
        AppNotification::unread()->update(['read_at' => now()]);
        return view('notifications.index', compact('notifications'));
    }

    public function markRead(AppNotification $notification)
    {
        $notification->update(['read_at' => now()]);
        return back();
    }

    public function clearAll()
    {
        AppNotification::query()->delete();
        return back()->with('success', 'All notifications cleared.');
    }
}
