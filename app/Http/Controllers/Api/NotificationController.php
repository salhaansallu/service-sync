<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function send(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'userId' => 'required|exists:api_users,id',
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|max:50',
            'data' => 'sometimes|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => 'VALIDATION_ERROR',
                'details' => $validator->errors()
            ], 400);
        }

        $notification = Notification::create([
            'user_id' => $request->userId,
            'title' => $request->title,
            'message' => $request->message,
            'type' => $request->type,
            'data' => $request->data ?? null,
            'is_read' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification sent successfully',
            'data' => [
                'id' => 'notification_' . $notification->id,
                'userId' => 'user_' . $notification->user_id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'isRead' => $notification->is_read,
                'createdAt' => $notification->created_at->toIso8601String(),
            ]
        ], 201);
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $unread = $request->query('unread');
        $page = $request->query('page', 1);
        $limit = $request->query('limit', 10);

        $query = Notification::where('user_id', $user->id);

        if ($unread === 'true' || $unread === '1') {
            $query->where('is_read', false);
        }

        $notifications = $query->orderBy('created_at', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->count();

        $formattedNotifications = $notifications->map(function($notification) {
            return [
                'id' => 'notification_' . $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'type' => $notification->type,
                'isRead' => $notification->is_read,
                'data' => $notification->data,
                'createdAt' => $notification->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'success' => true,
            'data' => [
                'notifications' => $formattedNotifications,
                'unreadCount' => $unreadCount,
                'pagination' => [
                    'total' => $notifications->total(),
                    'page' => $notifications->currentPage(),
                    'limit' => $notifications->perPage(),
                    'totalPages' => $notifications->lastPage(),
                ]
            ]
        ], 200);
    }

    public function markAsRead(Request $request, $notificationId)
    {
        $user = $request->user();
        $notification = Notification::where('id', $notificationId)
            ->where('user_id', $user->id)
            ->first();

        if (!$notification) {
            return response()->json([
                'success' => false,
                'message' => 'Notification not found',
                'error' => 'NOTIFICATION_NOT_FOUND'
            ], 404);
        }

        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'message' => 'Notification marked as read'
        ], 200);
    }

    public function markAllAsRead(Request $request)
    {
        $user = $request->user();
        
        Notification::where('user_id', $user->id)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json([
            'success' => true,
            'message' => 'All notifications marked as read'
        ], 200);
    }
}
