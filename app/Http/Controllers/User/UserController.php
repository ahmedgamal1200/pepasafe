<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function updateAttendance(Request $request)
    {

        // التحقق من صحة البيانات المرسلة
        $request->validate([
            'user_id' => 'required|integer',
            'is_attendance' => 'required|boolean',
        ]);

        // البحث عن المستخدم
        $user = User::find($request->user_id);

        if (!$user) {
            return response()->json(['status' => 'error', 'message' => 'User not found.'], 404);
        }

        // تحديث القيمة
        $user->is_attendance = $request->is_attendance;
        $user->save();

        return response()->json(['status' => 'success', 'message' => 'Attendance status updated successfully.']);
    }

}
