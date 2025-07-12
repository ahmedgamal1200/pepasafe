<?php

namespace App\Providers;

use App\Interfaces\Eventor\Auth\EventorRepositoryInterface;
use App\Repositories\Eventor\Auth\EventorAuthRepository;
use App\View\Composers\NotificationComposer;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // ربط ال interface ب repository
        $this->app->bind(
            EventorRepositoryInterface::class,
            EventorAuthRepository::class,
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // الجزء الخاص ب النتوفكيشن عشان يظهر النتوفكيشن في ال navbar
        View::composer('partials.auth-navbar', NotificationComposer::class);
    }
}
