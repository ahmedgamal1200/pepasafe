<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ApiConfig extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';

    // استخدام مفاتيح الترجمة
    protected static ?string $navigationLabel = 'ApiConfig.navigation_label';
    protected static ?string $navigationGroup = 'ApiConfig.navigation_group';
    protected static ?string $title = 'ApiConfig.page_title';

    protected static bool $shouldRegisterNavigation = true;

    protected static string $view = 'filament.pages.api-config';

    public array $data = [];

    // دوال لترجمة العناوين ديناميكياً
    public static function getNavigationLabel(): string
    {
        return trans_db(static::$navigationLabel);
    }

    public static function getNavigationGroup(): ?string
    {
        return trans_db(static::$navigationGroup);
    }

    public function getTitle(): string
    {
        return trans_db(static::$title);
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access');
    }

    public function mount(): void
    {
        $settings = \App\Models\ApiConfig::all()->pluck('value', 'key')->toArray();

        $this->form->fill([
            'data' => [
                'whatsapp_service' => $settings['whatsapp_service'] ?? 'authkey',

                // إضافة حقول إعدادات Authkey
                'authkey_instance_id' => $settings['authkey_instance_id'] ?? null,
                'authkey_token' => $settings['authkey_token'] ?? null,

                // إضافة حقول إعدادات Twilio
                'twilio_sid' => $settings['twilio_sid'] ?? null,
                'twilio_token' => $settings['twilio_token'] ?? null,
                'twilio_phone_number' => $settings['twilio_phone_number'] ?? null,

                // إضافة حقول إعدادات Beon.chat
                'beonchat_api_key' => $settings['beonchat_api_key'] ?? null,
                'beonchat_sender_id' => $settings['beonchat_sender_id'] ?? null,

                // حقول الإعدادات الأصلية الخاصة بالـ "Custom API" - تم استبعادها من الـ Mount لأنها لم تعد مُستخدمة
                // 'whatsapp_api_key' => $settings['whatsapp_api_key'] ?? null,
                // 'whatsapp_api_secret' => $settings['whatsapp_api_secret'] ?? null,
                // 'whatsapp_phone_number' => $settings['whatsapp_phone_number'] ?? null,

                'sms_service' => $settings['sms_service'] ?? 'beonchat',

                // Beon.chat SMS
                'beonchat_sms_api_key' => $settings['beonchat_sms_api_key'] ?? null,
                'beonchat_sms_sender_id' => $settings['beonchat_sms_sender_id'] ?? null,

                // Softex
                'softex_api_key' => $settings['softex_api_key'] ?? null,
                'softex_api_secret' => $settings['softex_api_secret'] ?? null,
                'softex_sender_id' => $settings['softex_sender_id'] ?? null,

                // Twilio SMS
                'twilio_sms_sid' => $settings['twilio_sms_sid'] ?? null,
                'twilio_sms_token' => $settings['twilio_sms_token'] ?? null,
                'twilio_sms_phone_number' => $settings['twilio_sms_phone_number'] ?? null,

                'mail_service' => $settings['mail_service'] ?? 'smtp',
                'zoho_api_key' => $settings['zoho_api_key'] ?? null,
                'zoho_from_address' => $settings['zoho_from_address'] ?? null,
                'zoho_from_name' => $settings['zoho_from_name'] ?? null,

                'brevo_api_key' => $settings['brevo_api_key'] ?? null,
                'brevo_from_address' => $settings['brevo_from_address'] ?? null,
                'brevo_from_name' => $settings['brevo_from_name'] ?? null,

                'sendpulse_api_id' => $settings['sendpulse_api_id'] ?? null,
                'sendpulse_api_secret' => $settings['sendpulse_api_secret'] ?? null,
                'sendpulse_from_address' => $settings['sendpulse_from_address'] ?? null,
                'sendpulse_from_name' => $settings['sendpulse_from_name'] ?? null,

                'mail_mailer' => $settings['mail_mailer'] ?? 'smtp',
                'mail_host' => $settings['mail_host'] ?? null,
                'mail_port' => $settings['mail_port'] ?? null,
                'mail_username' => $settings['mail_username'] ?? null,
                'mail_password' => $settings['mail_password'] ?? null,
                'mail_encryption' => $settings['mail_encryption'] ?? 'tls',
                'mail_from_address' => $settings['mail_from_address'] ?? null,
                'mail_from_name' => $settings['mail_from_name'] ?? null,
            ],
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make(trans_db('ApiConfig.tabs_title'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(trans_db('ApiConfig.tab_whatsapp'))
                            ->schema([
                                Forms\Components\Select::make('whatsapp_service')
                                    ->label(trans_db('ApiConfig.whatsapp_service_label'))
                                    ->options([
                                        'authkey' => 'Authkey.io',
                                        'twilio' => 'Twilio',
                                        'beonchat' => 'Beon.chat',
                                    ])
                                    ->default('authkey')
                                    ->reactive(),

                                // Authkey.io Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('authkey_instance_id')
                                            ->label(trans_db('ApiConfig.authkey_instance_id_label')),
                                        Forms\Components\TextInput::make('authkey_token')
                                            ->label(trans_db('ApiConfig.authkey_token_label'))
                                            ->password()
                                            ->revealable(),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('whatsapp_service') === 'authkey')
                                    ->columns(1),

                                // Twilio Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('twilio_sid')
                                            ->label(trans_db('ApiConfig.twilio_sid_label')),
                                        Forms\Components\TextInput::make('twilio_token')
                                            ->label(trans_db('ApiConfig.twilio_token_label'))
                                            ->password()
                                            ->revealable(),
                                        Forms\Components\TextInput::make('twilio_phone_number')
                                            ->label(trans_db('ApiConfig.twilio_whatsapp_phone_number_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('whatsapp_service') === 'twilio')
                                    ->columns(1),

                                // Beon.chat Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('beonchat_api_key')
                                            ->label(trans_db('ApiConfig.beonchat_whatsapp_api_key_label')),
                                        Forms\Components\TextInput::make('beonchat_sender_id')
                                            ->label(trans_db('ApiConfig.beonchat_whatsapp_sender_id_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('whatsapp_service') === 'beonchat')
                                    ->columns(1),
                            ]),

                        Forms\Components\Tabs\Tab::make(trans_db('ApiConfig.tab_sms'))
                            ->schema([
                                Forms\Components\Select::make('sms_service')
                                    ->label(trans_db('ApiConfig.sms_service_label'))
                                    ->options([
                                        'beonchat' => 'Beon.chat',
                                        'softex' => 'Softex',
                                        'twilio' => 'Twilio',
                                    ])
                                    ->default('beonchat')
                                    ->reactive(),

                                // Beon.chat SMS Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('beonchat_sms_api_key')
                                            ->label(trans_db('ApiConfig.beonchat_sms_api_key_label')),
                                        Forms\Components\TextInput::make('beonchat_sms_sender_id')
                                            ->label(trans_db('ApiConfig.beonchat_sms_sender_id_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('sms_service') === 'beonchat')
                                    ->columns(1),

                                // Softex Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('softex_api_key')
                                            ->label(trans_db('ApiConfig.softex_api_key_label')),
                                        Forms\Components\TextInput::make('softex_api_secret')
                                            ->label(trans_db('ApiConfig.softex_api_secret_label'))
                                            ->password()
                                            ->revealable(),
                                        Forms\Components\TextInput::make('softex_sender_id')
                                            ->label(trans_db('ApiConfig.softex_sender_id_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('sms_service') === 'softex')
                                    ->columns(1),

                                // Twilio SMS Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('twilio_sms_sid')
                                            ->label(trans_db('ApiConfig.twilio_sid_label')),
                                        Forms\Components\TextInput::make('twilio_sms_token')
                                            ->label(trans_db('ApiConfig.twilio_token_label'))
                                            ->password()
                                            ->revealable(),
                                        Forms\Components\TextInput::make('twilio_sms_phone_number')
                                            ->label(trans_db('ApiConfig.twilio_sms_phone_number_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('sms_service') === 'twilio')
                                    ->columns(1),
                            ]),

                        Forms\Components\Tabs\Tab::make(trans_db('ApiConfig.tab_email'))
                            ->schema([
                                Forms\Components\Select::make('mail_service')
                                    ->label(trans_db('ApiConfig.mail_service_label'))
                                    ->options([
                                        'zoho' => 'Zoho Mail',
                                        'brevo' => 'Brevo (formerly Sendinblue)',
                                        'sendpulse' => 'SendPulse',
                                        'smtp' => 'Custom SMTP',
                                    ])
                                    ->default('smtp')
                                    ->reactive() // Make the field reactive to trigger visibility changes
                                    ->afterStateUpdated(fn (callable $set) => $set('mail_mailer', null)), // Reset mailer when service changes

                                // Zoho Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('zoho_api_key')
                                            ->label(trans_db('ApiConfig.zoho_api_key_label')),
                                        Forms\Components\TextInput::make('zoho_from_address')
                                            ->label(trans_db('ApiConfig.from_email_address_label'))
                                            ->email(),
                                        Forms\Components\TextInput::make('zoho_from_name')
                                            ->label(trans_db('ApiConfig.from_name_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'zoho')
                                    ->columns(1),

                                // Brevo Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('brevo_api_key')
                                            ->label(trans_db('ApiConfig.brevo_api_key_label')),
                                        Forms\Components\TextInput::make('brevo_from_address')
                                            ->label(trans_db('ApiConfig.from_email_address_label'))
                                            ->email(),
                                        Forms\Components\TextInput::make('brevo_from_name')
                                            ->label(trans_db('ApiConfig.from_name_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'brevo')
                                    ->columns(1),

                                // SendPulse Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sendpulse_api_id')
                                            ->label(trans_db('ApiConfig.sendpulse_api_id_label')),
                                        Forms\Components\TextInput::make('sendpulse_api_secret')
                                            ->label(trans_db('ApiConfig.sendpulse_api_secret_label'))
                                            ->password()
                                            ->revealable(),
                                        Forms\Components\TextInput::make('sendpulse_from_address')
                                            ->label(trans_db('ApiConfig.from_email_address_label'))
                                            ->email(),
                                        Forms\Components\TextInput::make('sendpulse_from_name')
                                            ->label(trans_db('ApiConfig.from_name_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'sendpulse')
                                    ->columns(1),

                                // SMTP Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('mail_mailer')
                                            ->label(trans_db('ApiConfig.mail_driver_label'))
                                            ->default('smtp')
                                            ->placeholder(trans_db('ApiConfig.mail_driver_placeholder')),
                                        Forms\Components\TextInput::make('mail_host')
                                            ->label(trans_db('ApiConfig.smtp_host_label'))
                                            ->placeholder(trans_db('ApiConfig.smtp_host_placeholder')),
                                        Forms\Components\TextInput::make('mail_port')
                                            ->label(trans_db('ApiConfig.smtp_port_label'))
                                            ->numeric()
                                            ->placeholder(trans_db('ApiConfig.smtp_port_placeholder')),
                                        Forms\Components\TextInput::make('mail_username')
                                            ->label(trans_db('ApiConfig.smtp_username_label'))
                                            ->placeholder(trans_db('ApiConfig.smtp_username_placeholder')),
                                        Forms\Components\TextInput::make('mail_password')
                                            ->label(trans_db('ApiConfig.smtp_password_label'))
                                            ->password()
                                            ->revealable()
                                            ->placeholder(trans_db('ApiConfig.smtp_password_placeholder')),
                                        Forms\Components\Select::make('mail_encryption')
                                            ->label(trans_db('ApiConfig.encryption_label'))
                                            ->options([
                                                'tls' => 'TLS',
                                                'ssl' => 'SSL',
                                                'null' => trans_db('ApiConfig.encryption_none'),
                                            ]),
                                        Forms\Components\TextInput::make('mail_from_address')
                                            ->label(trans_db('ApiConfig.from_email_address_label'))
                                            ->email(),
                                        Forms\Components\TextInput::make('mail_from_name')
                                            ->label(trans_db('ApiConfig.from_name_label')),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'smtp')
                                    ->columns(1),
                            ]),

                    ]),
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $existingSettings = \App\Models\ApiConfig::all()->pluck('value', 'key')->toArray();

            foreach ($this->data as $key => $value) {
                // حفظ البيانات الجديدة في قاعدة البيانات
                $newValue = is_array($value) ? json_encode($value) : $value;
                $oldValue = $existingSettings[$key] ?? null;

                if ($newValue != $oldValue) {
                    \App\Models\ApiConfig::updateOrCreate(
                        ['key' => $key],
                        ['value' => $newValue]
                    );
                }
            }

            // استخدام مفاتيح الترجمة للإشعارات
            Notification::make()
                ->success()
                ->title(trans_db('ApiConfig.success_title'))
                ->body(trans_db('ApiConfig.success_body'))
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title(trans_db('ApiConfig.error_title'))
                ->body(trans_db('ApiConfig.error_body') . $e->getMessage())
                ->send();
        }
    }
}
