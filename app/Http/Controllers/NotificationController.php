<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function index(Request $request): View
    {
        $notifications = Notification::where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        return view('notifications.index', compact('notifications'));
    }

    public function markRead(Notification $notification): RedirectResponse
    {
        if ($notification->user_id === auth()->id()) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return back();
    }

    public function markAllRead(): RedirectResponse
    {
        Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return back()->with('success', 'All notifications marked as read.');
    }
}
