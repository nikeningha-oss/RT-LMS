<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    /**
     * Get all notifications for the authenticated user
     */
    public function index()
    {
        try {
            if (!Auth::check()) {
                return response()->json([
                    'notifications' => [],
                    'unreadCount' => 0
                ], 200);
            }

            $notifications = Notification::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc')
                ->limit(50)
                ->get();

            $unreadCount = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json([
                'notifications' => $notifications,
                'unreadCount' => $unreadCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching notifications: ' . $e->getMessage());
            return response()->json([
                'notifications' => [],
                'unreadCount' => 0
            ], 200);
        }
    }

    /**
     * Mark a specific notification as read
     */
    public function markAsRead($id)
    {
        try {
            $notification = Notification::where('user_id', Auth::id())
                ->where('id', $id)
                ->first();

            if ($notification) {
                $notification->is_read = true;
                $notification->save();
                return response()->json(['success' => true]);
            }

            return response()->json(['success' => false, 'message' => 'Notification not found'], 404);
        } catch (\Exception $e) {
            Log::error('Error marking notification as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        try {
            Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->update(['is_read' => true]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Error marking all as read: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Server error'], 500);
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount()
    {
        try {
            if (!Auth::check()) {
                return response()->json(['count' => 0], 200);
            }

            $count = Notification::where('user_id', Auth::id())
                ->where('is_read', false)
                ->count();

            return response()->json(['count' => $count], 200);
        } catch (\Exception $e) {
            Log::error('Error getting unread count: ' . $e->getMessage());
            return response()->json(['count' => 0], 200);
        }
    }
}