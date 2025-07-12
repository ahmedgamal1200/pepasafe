<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
use App\Notifications\InsufficientBalanceNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markAsRead(Request $request, string $id): JsonResponse
    {
        $user = Auth::user();
        $notification = $user->notifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            // بما إن ده مش API JSON Response، ممكن ترجع رسالة نجاح بسيطة
            return response()->json(['message' => 'Notification marked as read.', 'notification_id' => $id]);
        }

        return response()->json(['message' => 'Notification not found or unauthorized.'], 404);
    }


    public function markAllAsRead(Request $request): JsonResponse
    {
        Auth::user()->unreadNotifications->markAsRead();
        return response()->json(['message' => 'All notifications marked as read.']);
    }
}
