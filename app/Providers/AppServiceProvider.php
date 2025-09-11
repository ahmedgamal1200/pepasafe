<?php

namespace App\Providers;

use App\Interfaces\Eventor\Auth\EventorRepositoryInterface;
use App\Models\Plan;
use App\Observers\PlanObserver;
use App\Repositories\Eventor\AttendanceDocumentRepository;
use App\Repositories\Eventor\AttendanceTemplateRepository;
use App\Repositories\Eventor\Auth\EventorAuthRepository;
use App\Repositories\Eventor\DocumentRepository;
use App\Repositories\Eventor\DocumentTemplateRepository;
use App\Repositories\Eventor\EventRepository;
use App\Repositories\Eventor\RecipientRepository;
use App\Repositories\Eventor\SubscriptionRepository;
use App\Services\CertificateDispatchService;
use App\Services\DocumentGenerationService;
use App\Services\EventService;
use App\Services\RecipientService;
use App\Services\SubscriptionService;
use App\Services\TemplateService;
use App\View\Composers\NotificationComposer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        // Repositories
        $this->app->bind(EventRepository::class);
        $this->app->bind(DocumentTemplateRepository::class);
        $this->app->bind(AttendanceTemplateRepository::class);
        $this->app->bind(RecipientRepository::class);
        $this->app->bind(DocumentRepository::class);
        $this->app->bind(AttendanceDocumentRepository::class);
        $this->app->bind(SubscriptionRepository::class);

        // Services
        $this->app->bind(EventService::class, EventService::class);
        $this->app->bind(TemplateService::class, TemplateService::class);
        $this->app->bind(DocumentGenerationService::class, DocumentGenerationService::class);
        $this->app->bind(RecipientService::class, RecipientService::class);
        $this->app->bind(CertificateDispatchService::class, CertificateDispatchService::class);
        $this->app->bind(SubscriptionService::class, SubscriptionService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // الجزء الخاص ب النتوفكيشن عشان يظهر النتوفكيشن في ال navbar
        View::composer('partials.auth-navbar', NotificationComposer::class);
        Plan::observe(PlanObserver::class);
    }
}
