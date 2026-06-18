<?php

declare(strict_types=1);

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        try {
            $notifications = Notification::where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->paginate($request->input('per_page', 20));

            return response()->json([
                'success' => true,
                'data' => $notifications->items(),
                'message' => 'Notifications retrieved successfully.',
                'meta' => [
                    'current_page' => $notifications->currentPage(),
                    'last_page' => $notifications->lastPage(),
                    'per_page' => $notifications->perPage(),
                    'total' => $notifications->total(),
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve notifications.',
            ], 500);
        }
    }

    public function markAsRead(Request $request, int $id): JsonResponse
    {
        try {
            $notification = Notification::where('user_id', auth()->id())->findOrFail($id);
            $notification->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'Notification marked as read.',
            ], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Notification not found.',
            ], 404);
        }
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        try {
            Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json([
                'success' => true,
                'data' => null,
                'message' => 'All notifications marked as read.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to mark notifications as read.',
            ], 500);
        }
    }

    public function unreadCount(Request $request): JsonResponse
    {
        try {
            $count = Notification::where('user_id', auth()->id())
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'success' => true,
                'data' => ['count' => $count],
                'message' => 'Unread count retrieved.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'data' => null,
                'message' => 'Failed to retrieve unread count.',
            ], 500);
        }
    }
}
