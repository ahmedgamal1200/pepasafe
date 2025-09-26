<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Eventor\DocumentGenerationController;
use App\Http\Controllers\Eventor\DocumentVerificationController;
use App\Http\Controllers\Eventor\EventController;
use App\Http\Controllers\Eventor\HomeController;
use App\Http\Controllers\Eventor\NotificationController;
use App\Http\Controllers\Eventor\Wallet\WalletController;
use App\Http\Controllers\Eventor\Wallet\WalletRechargeRequestController;
use App\Http\Controllers\Pages\DocumentController;
use App\Http\Controllers\Pages\PrivacyAndPolicyController;
use App\Http\Controllers\Pages\TermsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteInfo\ContactUsController;
use App\Http\Controllers\User\RegisteredUserController;
use App\Http\Controllers\User\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('homeForGuests'); // أو أي صفحة عامة
});

Route::get('/u/{user:slug}', [ProfileController::class, 'showProfileToGuest'])->name('showProfileToGuest');
Route::get('/documents/{uuid}', [DocumentController::class, 'show'])->name('documents.show');
// في routes/web.php مثلاً
Route::get('/attendance/{uuid}', [AttendanceController::class, 'show'])->name('attendance.show');

Route::view('pry', 'pry')->name('pry');

Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // اخفاء الشهادة من البروفايل او اظهارها للاخرين
    Route::patch('/documents/{document}/toggle-visibility', [DocumentController::class, 'toggleVisibility'])
        ->name('documents.toggleVisibility');

    Route::patch('/events/{event}/toggle-visibility', [EventController::class, 'toggleVisibility'])
        ->name('events.toggleVisibility');

    Route::get('/profile/{slug}', [ProfileController::class, 'generateLinkToShare'])->name('profile.public');

    Route::get('/cookie-accept', function () {
        return response('OK')->cookie('cookie_consent', true, 60 * 24 * 365); // سنة
    })->name('cookie.accept');

    Route::post('/cookie-custom', function (Request $request) {
        // هنا هتقدر تستقبل البيانات من الـ Request
        $analytics = $request->input('analytics');
        $marketing = $request->input('marketing');

        // هنا هتضيف الكوكيز بتاعتك بناءً على القيم اللي جاتلك
        $response = response('OK');
        $response->cookie('user_consent', 'custom', 60 * 24 * 365); // كوكي لتأكيد التخصيص

        if ($analytics) {
            $response->cookie('analytics_enabled', true, 60 * 24 * 365);
        } else {
            // لو المستخدم اختار انه مايوافقش، ممكن تحذف الكوكي
            $response->cookie('analytics_enabled', null, -1);
        }

        if ($marketing) {
            $response->cookie('marketing_enabled', true, 60 * 24 * 365);
        } else {
            $response->cookie('marketing_enabled', null, -1);
        }

        return $response;
    })->name('cookie.custom');

    // OTP لتحقق من الايميل والهاتف
    Route::get('/verify-otp', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'showOtpForm'])
        ->name('verify.otp')->withoutMiddleware('ensure.otp.verified');
    Route::post('/verify-otp', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'verifyOtp'])
        ->name('verify.otp.submit')->withoutMiddleware('ensure.otp.verified');
    Route::post('/resend-otp', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'resendOtp'])
        ->name('resend.otp')->withoutMiddleware('ensure.otp.verified');

});

Route::middleware(['auth', 'role:eventor|super admin|admin|employee'])->group(function () {
    // home Page for eventor
    Route::get('home', [HomeController::class, 'index'])->name('home.eventor');

    // show event
    Route::get('show-event/{event}', [EventController::class, 'show'])->name('showEvent');
    // edit event
    Route::get('edit-event/{event}', [EventController::class, 'edit'])->name('editEvent');

    // تفعيل الحضور للكل
    Route::post('/toggle-attendance', [EventController::class, 'toggleAttendance'])
        ->name('toggleAttendance');
    // delete event
    Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

    // wallet
    Route::get('wallet', [WalletController::class, 'eventorWallet'])->name('wallet');
    // Auto Renew
    Route::post('/subscription/auto-renew', [WalletController::class, 'toggleAutoRenew'])->name('subscription.autoRenew');

    // Renew Now
    Route::post('/subscription/renew-now', [WalletController::class, 'renewNow'])->name('subscription.renewNow');

    // Notifications
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-as-read');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-as-read');
    Route::get('/notifications/latest', [NotificationController::class, 'getLatestNotifications'])
        ->middleware('auth')
        ->name('notifications.latest');

    // contact us
    Route::post('contact-us', [ContactUsController::class, 'sendMessageToMail'])->name('contact-us');

    // Show Event Blade
    Route::get('create-event', [EventController::class, 'create'])->name('create-event');

    // Wallet Recharge Requests
    Route::post('wallet-recharge-request', [WalletRechargeRequestController::class, 'store'])->name('wallet-recharge-request');

    // Upgrade Plan Requests
    Route::post('wallet/plan-upgrade-request', [\App\Http\Controllers\Eventor\Wallet\PlanUpgradeRequestController::class, 'upgrade'])
        ->name('plan.upgrade.request');

    // Document Generation
    Route::post('document-generation', [DocumentGenerationController::class, 'store'])->name('document-generation.store');
    // اما اليوزر يضغط ع لينك الشهاده تظهرله
    Route::get('/documents/verify/{uuid}', [DocumentVerificationController::class, 'verify'])->name('documents.verify');

    Route::post('/update-attendance', [UserController::class, 'updateAttendance']);

    Route::get('/download-documents/{template}', [DocumentController::class, 'downloadAll'])->name('documents.download');

});

Route::prefix('auth')->group(function () {
    Route::get('/google', [SocialLoginController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/google/callback', [SocialLoginController::class, 'handleGoogleCallback']);
});

// Users
// register-for-user
Route::middleware('guest')->group(function () {
    Route::get('register-user', [RegisteredUserController::class, 'create'])
        ->name('register.user');

    Route::post('register-user', [RegisteredUserController::class, 'store']);

    Route::get('home-for-guests', [HomeController::class, 'homeForGuests'])->name('homeForGuests');

});

Route::middleware('auth')->group(function () {
    //    Route::view('home/users', 'users.home')->name('home.users');
    Route::get('/home/users', [DocumentController::class, 'index'])->name('home.users');

});

Route::post('/calculate-document-price', [DocumentController::class, 'calculateDocumentPrice'])
    ->middleware('auth');

// about
Route::get('about', [AboutController::class, 'index'])->name('about');
Route::get('terms', [TermsController::class, 'index'])->name('terms');
Route::get('privacy', [PrivacyAndPolicyController::class, 'index'])->name('privacy');

require __DIR__.'/auth.php';
