<?php

namespace App\Http\Controllers\Eventor;

use App\Http\Controllers\Controller;
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

    public function getLatestNotifications()
    {
        $notifications = Auth::user()->unreadNotifications->map(function ($notification) {
            // التأكد من تطبيق المنطقة الزمنية (Africa/Cairo)
            $notification->created_at_formatted = $notification->created_at->diffForHumans();

            // إذا كنت تحتاج التوقيت الخام في الـ JS، يجب أن يكون بتوقيت القاهرة
            // هذا السطر مهم لضمان أن الـ JS يتعامل مع وقت القاهرة بدلاً من UTC
            $notification->created_at_cairo = $notification->created_at->setTimezone('Africa/Cairo')->toDateTimeString();

            return $notification;
        });

        // ستحتاج إلى إرجاع مجموعة البيانات كـ Array وليس كـ Collection
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications->toArray(), // استخدام toArray ضروري لعرض الحقول الجديدة
        ]);
    }
}
