<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ApiConfig extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
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
                'whatsapp_api_key' => $settings['whatsapp_api_key'] ?? null,
                'whatsapp_api_secret' => $settings['whatsapp_api_secret'] ?? null,
                'whatsapp_phone_number' => $settings['whatsapp_phone_number'] ?? null,

                'sms_api_key' => $settings['sms_api_key'] ?? null,
                'sms_api_secret' => $settings['sms_api_secret'] ?? null,
                'sms_sender_id' => $settings['sms_sender_id'] ?? null,

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
                                Forms\Components\TextInput::make('whatsapp_api_key')->label('API Key'),
                                Forms\Components\TextInput::make('whatsapp_api_secret')->label('API Secret')->password()->revealable(),
                                Forms\Components\TextInput::make('whatsapp_phone_number')->label('Sender Number'),
                            ]),

                        Forms\Components\Tabs\Tab::make('SMS')
                            ->schema([
                                Forms\Components\TextInput::make('sms_api_key')->label('API Key'),
                                Forms\Components\TextInput::make('sms_api_secret')->label('SMS API Secret')->password()->revealable(),
                                Forms\Components\TextInput::make('sms_sender_id')->label('Sender ID'),
                            ]),

                        Forms\Components\Tabs\Tab::make('SMTP Email')
                            ->schema([
                                Forms\Components\TextInput::make('mail_mailer')
                                    ->label('Mail Driver (MAIL_MAILER)')
                                    ->default('smtp')
                                    ->placeholder('e.g. smtp'),

                                Forms\Components\TextInput::make('mail_host')
                                    ->label('SMTP Host (MAIL_HOST)')
                                    ->placeholder('e.g. smtp.gmail.com'),

                                Forms\Components\TextInput::make('mail_port')
                                    ->label('SMTP Port (MAIL_PORT)')
                                    ->numeric()
                                    ->placeholder('e.g. 587 or 465'),

                                Forms\Components\TextInput::make('mail_username')
                                    ->label('SMTP Username (MAIL_USERNAME)')
                                    ->placeholder('e.g. your-email@gmail.com'),

                                Forms\Components\TextInput::make('mail_password')
                                    ->label('SMTP Password (MAIL_PASSWORD)')
                                    ->password()
                                    ->revealable()
                                    ->placeholder('App password or SMTP password'),

                                Forms\Components\Select::make('mail_encryption')
                                    ->label('Encryption (MAIL_ENCRYPTION)')
                                    ->options([
                                        'tls' => 'TLS',
                                        'ssl' => 'SSL',
                                        'null' => 'None',
                                    ])
                                    ->placeholder('Select encryption method'),

                                Forms\Components\TextInput::make('mail_from_address')
                                    ->label('From Email Address (MAIL_FROM_ADDRESS)')
                                    ->email()
                                    ->placeholder('e.g. no-reply@yourdomain.com'),

                                Forms\Components\TextInput::make('mail_from_name')
                                    ->label('From Name (MAIL_FROM_NAME)')
                                    ->placeholder('e.g. Your App Name'),
                            ])

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
