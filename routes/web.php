<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Auth\SocialLoginController;
use App\Http\Controllers\Eventor\DocumentGenerationController;
use App\Http\Controllers\Eventor\DocumentVerificationController;
use App\Http\Controllers\Eventor\EventController;
use App\Http\Controllers\Eventor\HomeController;
use App\Http\Controllers\Eventor\NotificationController;
use App\Http\Controllers\Eventor\WalletController;
use App\Http\Controllers\Eventor\WalletRechargeRequestController;
use App\Http\Controllers\Pages\DocumentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiteInfo\ContactUsController;
use App\Http\Controllers\User\RegisteredUserController;
use App\Notifications\TestNotification;
use Illuminate\Support\Facades\Route;

use App\Models\User;


//Route::get('/test-notification', function () {
//    $user = User::find(4); // ID بتاعك
//    $user->notify(new TestNotification('إشعار لحظي 🔔'));
//    return 'تم الإرسال';
//});

Route::get('/', function () {
    return redirect()->route('homeForGuests'); // أو أي صفحة عامة
});

Route::get('/u/{user:slug}', [ProfileController::class, 'showProfileToGuest'])->name('showProfileToGuest');
Route::get('/documents/{uuid}', [DocumentController::class, 'show'])->name('documents.show');



Route::view('pry', 'pry')->name('pry');


Route::middleware('auth')->group(function () {
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');

    // اخفاء الشهادة من البروفايل او اظهارها للاخرين
    Route::patch('/documents/{document}/toggle-visibility', [DocumentController::class, 'toggleVisibility'])
        ->name('documents.toggleVisibility');
    Route::get('/profile/{slug}', [ProfileController::class, 'generateLinkToShare'])->name('profile.public');

    Route::get('/cookie-accept', function () {
        return response('OK')->cookie('cookie_consent', true, 60 * 24 * 365); // سنة
    })->name('cookie.accept');

    // OTP لتحقق من الايميل والهاتف
    Route::get('/verify-otp', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'showOtpForm'])
        ->name('verify.otp')->withoutMiddleware('ensure.otp.verified');;
    Route::post('/verify-otp', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'verifyOtp'])
        ->name('verify.otp.submit')->withoutMiddleware('ensure.otp.verified');;
    Route::post('/resend-otp', [\App\Http\Controllers\Auth\RegisteredUserController::class, 'resendOtp'])
        ->name('resend.otp')->withoutMiddleware('ensure.otp.verified');;



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

        // contact us
        Route::post('contact-us', [ContactUsController::class, 'sendMessageToMail'])->name('contact-us');

        // Show Event Blade
        Route::get('create-event', [EventController::class, 'create'])->name('create-event');

        // Wallet Recharge Requests
        Route::post('wallet-recharge-request', [WalletRechargeRequestController::class, 'store'])->name('wallet-recharge-request');

        // Document Generation
    Route::post('document-generation', [DocumentGenerationController::class, 'store'])->name('document-generation.store');
    // اما اليوزر يضغط ع لينك الشهاده تظهرله
    Route::get('/documents/verify/{uuid}', [DocumentVerificationController ::class, 'verify'])->name('documents.verify');



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

    // about
    Route::get('about', [AboutController::class, 'index'])->name('about');

    Route::get('home-for-guests', [HomeController::class, 'homeForGuests'])->name('homeForGuests');



});

Route::middleware('auth')->group(function () {
//    Route::view('home/users', 'users.home')->name('home.users');
    Route::get('/home/users',  [DocumentController::class, 'index'])->name('home.users');

});

Route::post('/calculate-document-price', [DocumentController::class, 'calculateDocumentPrice'])
    ->middleware('auth');




require __DIR__.'/auth.php';

