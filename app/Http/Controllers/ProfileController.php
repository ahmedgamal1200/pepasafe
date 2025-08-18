<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Document;
use App\Models\Event;
use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
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
        $documents = collect();
        $user = Auth::user();

        $templateCount = 0;
        $recipientCount = 0;
        $templates = collect();

        $documents = auth()->user()->documents()->with(['template.event', 'recipient'])->get();

        $events = Event::query()
            ->where('user_id', $request->user()->id)
            ->with(['documentTemplates', 'recipients'])
            ->get();

        if($user) {
            foreach ($events as $event) {
                $templateCount += $event->documentTemplates->count();
                $recipientCount += $event->recipients->count();
            }
        }
        else{
            $events = collect();
        }



        return view('profile.edit', [
            'user' => $request->user(),
            'documents' => $documents,
            'events' => $events,
            'templateCount' => $templateCount,
            'recipientCount' => $recipientCount,
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

        // لو حابب تتعامل مع bio صراحة (اختياري)
        if ($request->filled('bio')) {
            $request->user()->bio = $request->input('bio');
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

    public function generateLinkToShare($slug): \Illuminate\Contracts\View\View|Application|Factory
    {
        $user = User::where('slug', $slug)->firstOrFail();

        return view('profile.partials.share-profile', compact('user'));
    }

    public function showProfileToGuest(User $user): View
    {

        $templateCount = 0;
        $recipientCount = 0;
        $templates = collect();

        $documents = $user->documents()
            ->where('visible_on_profile', true)
            ->with(['template.event'])
            ->get();

        $events = Event::query()
            ->where('visible_on_profile', true)
            ->with(['documentTemplates', 'recipients'])
            ->get();

            foreach ($events as $event) {
                $templateCount += $event->documentTemplates->count();
                $recipientCount += $event->recipients->count();
            }



        return view('profile.partials.show-profile-guest', compact(
            'user',
            'documents',
            'events',
            'templateCount',
            'recipientCount',
        ));
    }

}
