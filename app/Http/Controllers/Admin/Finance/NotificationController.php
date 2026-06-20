<?php

namespace App\Http\Controllers\Admin\Finance;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->latest('created_at')
            ->paginate(20);

        return view('finance.notifications.index', compact('notifications'));
    }

    public function unreadCount(Request $request)
    {
        $count = Notification::where('user_id', Auth::id())->where('is_read', false)->count();

        $notifications = Notification::where('user_id', Auth::id())->latest('created_at')
            ->when($request->filled('limit'), fn($q) => $q->limit((int) $request->limit))
            ->get()
            ->map(fn($n) => [
                'id' => $n->id,
                'title' => $n->title,
                'message' => $n->message,
                'is_read' => $n->is_read,
                'time_ago' => $n->created_at->diffForHumans(),
            ]);

        return response()->json(['count' => $count, 'notifications' => $notifications]);
    }

    public function markAsRead(Notification $notification)
    {
        if ($notification->user_id !== Auth::id()) {
            abort(403);
        }

        $notification->update(['is_read' => true, 'read_at' => now()]);

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true, 'read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
