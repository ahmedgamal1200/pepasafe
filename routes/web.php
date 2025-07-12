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
use Illuminate\Support\Facades\Route;




Route::view('pry', 'pry')->name('pry');



Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


Route::middleware(['auth', 'role:eventor|super admin|admin|employee'])->group(function () {
        // home Page for eventor
        Route::get('home', [HomeController::class, 'index'])->name('home.eventor');

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
});

Route::middleware('auth')->group(function () {
//    Route::view('home/users', 'users.home')->name('home.users');
    Route::get('/home/users',  [DocumentController::class, 'index'])->name('home.users');
    Route::get('/documents/{uuid}', [DocumentController::class, 'show'])->name('documents.show');

});




require __DIR__.'/auth.php';

