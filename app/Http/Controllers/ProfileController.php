<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {

        $request->user()->fill($request->validated());

        if ($request->hasFile('profile_picture')) {

            if ($request->user()->profile_picture) {
                // Storage::disk('public')->delete() بتحذف الملف من مجلد storage/app/public/
                Storage::disk('public')->delete($request->user()->profile_picture);
            }

            $path = $request->file('profile_picture')->store('avatars', 'public');

            // 3. تخزين مسار الصورة الجديد في عمود 'profile_picture' في الـ User Model
            $request->user()->profile_picture = $path;
        }
        // ********** نهاية التعامل مع الصورة الشخصية **********


        // التعامل مع الإيميل (لو اتغير بيخلي حالة التحقق بـ null)
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        // إعادة التوجيه لصفحة تعديل البروفايل مع رسالة نجاح
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
