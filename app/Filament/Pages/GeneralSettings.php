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

    protected static ?string $title = 'System Settings';

    protected static ?string $navigationGroup = 'Settings';

    // 🟢 لازم المتغير يكون معرف
    public $profile_page_enabled;

    public $show_bio_public;

    public $show_documents_in_profile;

    public $user_can_share_here_profile;

    public $email_otp_active;

    public $sms_otp_active;

    public $show_events_in_profile;

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyPermission([
            'full access',
        ]);
    }

    public function mount(): void
    {
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
        return [
            Section::make('General Settings')
                ->schema([
                    Toggle::make('profile_page_enabled')
                        ->label('Enable Public Profile Page')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'profile_page_enabled'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        })
                        ->formatStateUsing(fn () => Setting::where('key', 'profile_page_enabled')->value('value') === '1'),

                    Toggle::make('show_bio_public')
                        ->label('Show Bio Publicly')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'show_bio_public'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        }),

                    Toggle::make('show_documents_in_profile')
                        ->label('Show Documents in Profile')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'show_documents_in_profile'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        }),

                    Toggle::make('show_events_in_profile')
                        ->label('Show Events in Profile')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'show_events_in_profile'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        }),

                    Toggle::make('user_can_share_here_profile')
                        ->label('User Can Share Here Profile')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'user_can_share_here_profile'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        }),
                    Toggle::make('email_otp_active')
                        ->label('Enable Email OTP Verification')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'email_otp_active'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        }),

                    Toggle::make('sms_otp_active')
                        ->label('Enable SMS OTP Verification')
                        ->reactive()
                        ->afterStateUpdated(function ($state) {
                            Setting::updateOrCreate(
                                ['key' => 'sms_otp_active'],
                                ['value' => $state ? '1' : '0']
                            );

                            Notification::make()
                                ->title('Setting updated successfully')
                                ->success()
                                ->send();
                        }),

                ]),
        ];
    }

    public function getFormModel(): string
    {
        return static::class;
    }
}
