<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ApiConfig extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-code-bracket';
    protected static ?string $navigationLabel = 'API Configuration';
    protected static ?string $navigationGroup = 'Settings';
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.api-config';

    public array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access');
    }

    public function mount(): void
    {
        $settings =  \App\Models\ApiConfig::all()->pluck('value', 'key')->toArray();

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

                // حقول الإعدادات الأصلية الخاصة بالـ "Custom API"
                'whatsapp_api_key' => $settings['whatsapp_api_key'] ?? null,
                'whatsapp_api_secret' => $settings['whatsapp_api_secret'] ?? null,
                'whatsapp_phone_number' => $settings['whatsapp_phone_number'] ?? null,



                'sms_service' => $settings['sms_service'] ?? 'beonchat',

                // Beon.chat
                'beonchat_sms_api_key' => $settings['beonchat_api_key'] ?? null,
                'beonchat_sms_sender_id' => $settings['beonchat_sender_id'] ?? null,

                // Softex
                'softex_api_key' => $settings['softex_api_key'] ?? null,
                'softex_api_secret' => $settings['softex_api_secret'] ?? null,
                'softex_sender_id' => $settings['softex_sender_id'] ?? null,

                // Twilio SMS (أسماء مميزة)
                'twilio_sms_sid' => $settings['twilio_sms_sid'] ?? null,
                'twilio_sms_token' => $settings['twilio_sms_token'] ?? null,
                'twilio_sms_phone_number' => $settings['twilio_sms_phone_number'] ?? null,

                'mail_service'      => $settings['mail_service'] ?? 'smtp',
                'zoho_api_key'      => $settings['zoho_api_key'] ?? null,
                'brevo_api_key'     => $settings['brevo_api_key'] ?? null,
                'sendpulse_api_id'  => $settings['sendpulse_api_id'] ?? null,
                'sendpulse_api_secret' => $settings['sendpulse_api_secret'] ?? null,
                'mail_mailer'        => $settings['mail_mailer'] ?? 'smtp',
                'mail_host'          => $settings['mail_host'] ?? null,
                'mail_port'          => $settings['mail_port'] ?? null,
                'mail_username'      => $settings['mail_username'] ?? null,
                'mail_password'      => $settings['mail_password'] ?? null,
                'mail_encryption'    => $settings['mail_encryption'] ?? 'tls',
                'mail_from_address'  => $settings['mail_from_address'] ?? null,
                'mail_from_name'     => $settings['mail_from_name'] ?? null,
            ]
        ]);
    }

    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make('API Configurations')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('WhatsApp')
                            ->schema([
                                Forms\Components\Select::make('whatsapp_service')
                                    ->label('WhatsApp Service Provider')
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
                                        Forms\Components\TextInput::make('authkey_instance_id')->label('Authkey Instance ID'),
                                        Forms\Components\TextInput::make('authkey_token')->label('Authkey Token')->password()->revealable(),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('whatsapp_service') === 'authkey')
                                    ->columns(1),

                                // Twilio Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('twilio_sid')->label('Twilio SID'),
                                        Forms\Components\TextInput::make('twilio_token')->label('Twilio Auth Token')->password()->revealable(),
                                        Forms\Components\TextInput::make('twilio_phone_number')->label('Twilio Phone Number'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('whatsapp_service') === 'twilio')
                                    ->columns(1),

                                // Beon.chat Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('beonchat_api_key')->label('Beon.chat API Key'),
                                        Forms\Components\TextInput::make('beonchat_sender_id')->label('Beon.chat Sender ID'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('whatsapp_service') === 'beonchat')
                                    ->columns(1),
                            ]),

                        Forms\Components\Tabs\Tab::make('SMS')
                            ->schema([
                                Forms\Components\Select::make('sms_service')
                                    ->label('SMS Service Provider')
                                    ->options([
                                        'beonchat' => 'Beon.chat',
                                        'softex' => 'Softex',
                                        'twilio' => 'Twilio',
                                    ])
                                    ->default('beonchat')
                                    ->reactive(),

                                // Beon.chat Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('beonchat_sms_api_key')->label('Beon.chat API Key'),
                                        Forms\Components\TextInput::make('beonchat_sms_sender_id')->label('Beon.chat Sender ID'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('sms_service') === 'beonchat')
                                    ->columns(1),

                                // Softex Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('softex_api_key')->label('Softex API Key'),
                                        Forms\Components\TextInput::make('softex_api_secret')->label('Softex API Secret')->password()->revealable(),
                                        Forms\Components\TextInput::make('softex_sender_id')->label('Softex Sender ID'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('sms_service') === 'softex')
                                    ->columns(1),

                                // Twilio Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('twilio_sms_sid')->label('Twilio SID'),
                                        Forms\Components\TextInput::make('twilio_sms_token')->label('Twilio Auth Token')->password()->revealable(),
                                        Forms\Components\TextInput::make('twilio_sms_phone_number')->label('Twilio Phone Number'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('sms_service') === 'twilio')
                                    ->columns(1),
                            ]),

                        Forms\Components\Tabs\Tab::make('Email Services')
                            ->schema([
                                Forms\Components\Select::make('mail_service')
                                    ->label('Email Service Provider')
                                    ->options([
                                        'zoho' => 'Zoho Mail',
                                        'brevo' => 'Brevo (formerly Sendinblue)',
                                        'sendpulse' => 'SendPulse',
                                        'smtp' => 'Custom SMTP',
                                    ])
                                    ->default('smtp')
                                    ->reactive() // Make the field reactive to trigger visibility changes
                                    ->afterStateUpdated(function (callable $set) {
                                        // You might need this to reset other fields when the service changes
                                        // $set('zoho_api_key', null);
                                        // $set('brevo_api_key', null);
                                    }),

                                // Zoho Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('zoho_api_key')->label('Zoho API Key'),
                                        Forms\Components\TextInput::make('zoho_from_address')->label('From Email Address'),
                                        Forms\Components\TextInput::make('zoho_from_name')->label('From Name'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'zoho')
                                    ->columns(1),

                                // Brevo Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('brevo_api_key')->label('Brevo API Key'),
                                        Forms\Components\TextInput::make('brevo_from_address')->label('From Email Address'),
                                        Forms\Components\TextInput::make('brevo_from_name')->label('From Name'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'brevo')
                                    ->columns(1),

                                // SendPulse Configuration
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('sendpulse_api_id')->label('SendPulse API ID'),
                                        Forms\Components\TextInput::make('sendpulse_api_secret')->label('SendPulse API Secret')->password()->revealable(),
                                        Forms\Components\TextInput::make('sendpulse_from_address')->label('From Email Address'),
                                        Forms\Components\TextInput::make('sendpulse_from_name')->label('From Name'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'sendpulse')
                                    ->columns(1),

                                // SMTP Configuration (Your original code)
                                Forms\Components\Group::make()
                                    ->schema([
                                        Forms\Components\TextInput::make('mail_mailer')->label('Mail Driver')->default('smtp')->placeholder('e.g. smtp'),
                                        Forms\Components\TextInput::make('mail_host')->label('SMTP Host')->placeholder('e.g. smtp.gmail.com'),
                                        Forms\Components\TextInput::make('mail_port')->label('SMTP Port')->numeric()->placeholder('e.g. 587'),
                                        Forms\Components\TextInput::make('mail_username')->label('SMTP Username')->placeholder('e.g. your-email@gmail.com'),
                                        Forms\Components\TextInput::make('mail_password')->label('SMTP Password')->password()->revealable()->placeholder('App password'),
                                        Forms\Components\Select::make('mail_encryption')->label('Encryption')->options(['tls' => 'TLS', 'ssl' => 'SSL', 'null' => 'None']),
                                        Forms\Components\TextInput::make('mail_from_address')->label('From Email Address')->email(),
                                        Forms\Components\TextInput::make('mail_from_name')->label('From Name'),
                                    ])
                                    ->visible(fn (\Filament\Forms\Get $get) => $get('mail_service') === 'smtp')
                                    ->columns(1),
                            ]),

                    ])
            ])
            ->statePath('data');
    }

    public function submit(): void
    {
        try {
            $existingSettings =  \App\Models\ApiConfig::all()->pluck('value', 'key')->toArray();

            foreach ($this->data as $key => $value) {
                $newValue = is_array($value) ? json_encode($value) : $value;
                $oldValue = $existingSettings[$key] ?? null;

                if ($newValue != $oldValue) {
                    \App\Models\ApiConfig::updateOrCreate(
                        ['key' => $key],
                        ['value' => $newValue]
                    );
                }
            }

            Notification::make()
                ->success()
                ->title('Settings saved successfully!')
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Error saving settings.')
                ->body($e->getMessage())
                ->send();
        }
    }
}
