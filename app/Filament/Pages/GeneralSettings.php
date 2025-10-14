<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GeneralSettings extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $view = 'filament.pages.general-settings';

    // استخدام مفاتيح الترجمة
    protected static ?string $title = 'settings.system_settings_title';
    protected static ?string $navigationGroup = 'settings.navigation_group';

    // 🟢 تعريف المتغيرات
    public $profile_page_enabled;
    public $show_bio_public;
    public $show_documents_in_profile;
    public $user_can_share_here_profile;
    public $email_otp_active;
    public $sms_otp_active;
    public $show_events_in_profile;


    public static function getNavigationGroup(): ?string
    {
        return trans_db(static::$navigationGroup);
    }
    public function getTitle(): string
    {
        return trans_db(static::$title);
    }

    public static function getNavigationLabel(): string
    {
        return trans_db('settings.system_settings_title');
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
        ]);
    }

    public function mount(): void
    {
        // تعيين الحالة الأولية للنماذج
        $this->profile_page_enabled = Setting::where('key', 'profile_page_enabled')->value('value') === '1';
        $this->show_bio_public = Setting::where('key', 'show_bio_public')->value('value') === '1';
        $this->show_documents_in_profile = Setting::where('key', 'show_documents_in_profile')->value('value') === '1';
        $this->user_can_share_here_profile = Setting::where('key', 'user_can_share_here_profile')->value('value') === '1';
        $this->email_otp_active = Setting::where('key', 'email_otp_active')->value('value') === '1';
        $this->sms_otp_active = Setting::where('key', 'sms_otp_active')->value('value') === '1';
        $this->show_events_in_profile = Setting::where('key', 'show_events_in_profile')->value('value') === '1';
    }

    public function getFormSchema(): array
    {
        // استخدام مفاتيح الترجمة في هيكل النموذج
        return [
            Section::make(trans_db('settings.general_settings_section'))
                ->schema([
                    Toggle::make('profile_page_enabled')
                        ->label(trans_db('settings.profile_page_enabled'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('profile_page_enabled', $state))
                        ->formatStateUsing(fn () => Setting::where('key', 'profile_page_enabled')->value('value') === '1'),

                    Toggle::make('show_bio_public')
                        ->label(trans_db('settings.show_bio_public'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('show_bio_public', $state)),

                    Toggle::make('show_documents_in_profile')
                        ->label(trans_db('settings.show_documents_in_profile'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('show_documents_in_profile', $state)),

                    Toggle::make('show_events_in_profile')
                        ->label(trans_db('settings.show_events_in_profile'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('show_events_in_profile', $state)),

                    Toggle::make('user_can_share_here_profile')
                        ->label(trans_db('settings.user_can_share_here_profile'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('user_can_share_here_profile', $state)),

                    Toggle::make('email_otp_active')
                        ->label(trans_db('settings.email_otp_active'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('email_otp_active', $state)),

                    Toggle::make('sms_otp_active')
                        ->label(trans_db('settings.sms_otp_active'))
                        ->reactive()
                        ->afterStateUpdated(fn ($state) => $this->updateSetting('sms_otp_active', $state)),
                ]),
        ];
    }

    public function updateSetting($key, $state): void
    {
        Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $state ? '1' : '0']
        );

        Notification::make()
            ->title(trans_db('settings.update_success_title'))
            ->success()
            ->send();
    }

    public function getFormModel(): string
    {
        return static::class;
    }
}
