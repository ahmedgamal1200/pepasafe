<?php

namespace App\Filament\Pages;


use App\Jobs\ScheduleDashboardNotificationJob;
use Filament\Pages\Page;
use Filament\Forms;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Filament\Notifications\Notification;
use App\Notifications\CustomAdminNotification;



class SendMessage extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationLabel = 'Send Notification';
    protected static ?string $navigationGroup = 'Notifications';
    protected static bool $shouldRegisterNavigation = true;
    protected static string $view = 'filament.pages.send-message';

    public $users = [];
    public $channel = 'email';
    public $message = '';
    public $subject = '';

    public bool $send_to_all = false;
    public $scheduled_at;

    public static function canAccess(): bool
    {
        return auth()->user()?->can('full access');
    }


    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Toggle::make('send_to_all')
                ->label('Send to all users')
                ->reactive(), // علشان نقدر نغير اختيار المستخدمين بناءً عليه

            Forms\Components\DateTimePicker::make('scheduled_at')
                ->label('Schedule Date & Time')
                ->placeholder('Choose when to send the message')
                ->required(),


            Forms\Components\Select::make('users')
                ->label('Select users')
                ->multiple()
                ->options(User::all()->pluck('name', 'id'))
                ->searchable()
                ->required()
                ->visible(fn ($get) => !$get('send_to_all')), // لو "إرسال إلى الكل" مفعل، نخفي اختيار المستخدمين

            Forms\Components\Select::make('channel')
                ->label('Select channel')
                ->options([
                    'whatsapp' => 'WhatsApp',
                    'email' => 'Email',
                    'sms' => 'SMS',
                    'database' => 'Notification in system',
                ])
                ->required()
                ->searchable()
                ->reactive(),

            Forms\Components\TextInput::make('subject')
                ->label('Email Subject')
                ->placeholder('Enter the subject of the message')
                ->required()
                ->visible(fn ($get) => $get('channel') === 'email'),



            Forms\Components\Textarea::make('message')
                ->label('Email Message')
                ->required()
                ->rows(6),
        ];
    }


    public function submit()
    {
        if (!$this->send_to_all && empty($this->users)) {
            Notification::make()
                ->danger()
                ->title('No users selected')
                ->body('Please select at least one user or enable "Send to all".')
                ->send();
            return;
        }

        if ($this->scheduled_at) {
            \App\Jobs\ScheduleDashboardNotificationJob::dispatch(
                $this->channel,
                $this->message,
                $this->subject,
                $this->send_to_all ? [] : $this->users
            )->delay(Carbon::parse($this->scheduled_at));
        } else {
            \App\Jobs\ScheduleDashboardNotificationJob::dispatch(
                $this->channel,
                $this->message,
                $this->subject,
                $this->send_to_all ? [] : $this->users
            );
        }

        Notification::make()
            ->success()
            ->title('Scheduled successfully!')
            ->body('Your message has been scheduled for sending.')
            ->send();
    }



//    protected function sendEmail(User $user)
//    {
//        $settings = \App\Models\ApiConfig::whereIn('key', [
//            'mail_mailer',
//            'mail_host',
//            'mail_port',
//            'mail_username',
//            'mail_password',
//            'mail_encryption',
//            'mail_from_address',
//            'mail_from_name',
//        ])->pluck('value', 'key');
//
//        $requiredKeys = [
//            'mail_mailer',
//            'mail_host',
//            'mail_port',
//            'mail_username',
//            'mail_password',
//            'mail_encryption',
//            'mail_from_address',
//            'mail_from_name',
//        ];
//
//        foreach ($requiredKeys as $key) {
//            if (empty($settings[$key])) {
//                Notification::make()
//                    ->danger()
//                    ->title('Missing Email Config')
//                    ->body("Missing config: $key")
//                    ->send();
//                return;
//            }
//        }
//
//        // ضبط الإعدادات مؤقتًا
//        config([
//            'mail.default' => $settings['mail_mailer'],
//            'mail.mailers.smtp.transport' => $settings['mail_mailer'],
//            'mail.mailers.smtp.host' => $settings['mail_host'],
//            'mail.mailers.smtp.port' => (int) $settings['mail_port'],
//            'mail.mailers.smtp.encryption' => $settings['mail_encryption'],
//            'mail.mailers.smtp.username' => $settings['mail_username'],
//            'mail.mailers.smtp.password' => $settings['mail_password'],
//            'mail.from.address' => $settings['mail_from_address'],
//            'mail.from.name' => $settings['mail_from_name'],
//        ]);
//
//        try {
//            Mail::raw($this->message, function ($message) use ($user) {
//                $message->to($user->email)
//                    ->subject($this->subject ?: 'Message Form PepaSafe');
//            });
//
//            Notification::make()
//                ->success()
//                ->title('Email sent successfully!')
//                ->send();
//        } catch (\Exception $e) {
//            Notification::make()
//                ->danger()
//                ->title('Email failed')
//                ->body($e->getMessage())
//                ->send();
//        }
//    }



//    protected function sendSms(User $user)
//    {
//        $settings = \App\Models\ApiConfig::whereIn('key', [
//            'sms_api_key',          // Twilio SID
//            'sms_api_secret',       // Twilio Token
//            'sms_sender_number',    // Twilio sender number (رقم الشراء من Twilio)
//        ])->pluck('value', 'key');
//
//        $sid     = $settings['sms_api_key'] ?? null;
//        $token   = $settings['sms_api_secret'] ?? null;
//        $from    = $settings['sms_sender_number'] ?? null;
//        $to      = $user->phone ?? null;
////        $to      = '+201279678444';
//
//        if (!$sid || !$token || !$from || !$to) {
//            Notification::make()
//                ->danger()
//                ->title('Missing SMS Configuration')
//                ->body('Please make sure SID, Token, Sender, and User Phone are set correctly.')
//                ->send();
//            return;
//        }
//
//        try {
//            $twilio = new \Twilio\Rest\Client($sid, $token);
//
//            $twilio->messages->create(
//                $to, // الرقم المستلم بصيغة دولية
//                [
//                    'from' => $from,
//                    'body' => $this->message,
//                ]
//            );
//
//            Notification::make()
//                ->success()
//                ->title('SMS sent successfully!')
//                ->send();
//
//        } catch (\Exception $e) {
//            Notification::make()
//                ->danger()
//                ->title('SMS Sending Failed')
//                ->body($e->getMessage())
//                ->send();
//        }
//    }
//
//
//
//    protected function sendWhatsApp(User $user)
//    {
//        // جلب الإعدادات من قاعدة البيانات حسب الأسماء الجديدة
//        $settings = \App\Models\ApiConfig::whereIn('key', [
//            'whatsapp_api_key',
//            'whatsapp_api_secret',
//            'whatsapp_phone_number',
//        ])->pluck('value', 'key');
//
//        // التحقق من وجود كل القيم
//        if (
//            empty($settings['whatsapp_api_key']) ||
//            empty($settings['whatsapp_api_secret']) ||
//            empty($settings['whatsapp_phone_number'])
//        ) {
//            Notification::make()
//                ->danger()
//                ->title('Missing WhatsApp Configuration')
//                ->body('Please make sure API Key, Secret, and Phone Number are configured correctly.')
//                ->send();
//            return;
//        }
//
//        try {
//            $sid    = $settings['whatsapp_api_key'];
//            $token  = $settings['whatsapp_api_secret'];
//            $from   = $settings['whatsapp_phone_number'];
//
//            $twilio = new \Twilio\Rest\Client($sid, $token);
//
//            $twilio->messages->create(
//                'whatsapp:' . '+201558281036',
//                [
//                    'from' => 'whatsapp:' . $from,
//                    'body' => $this->message,
//                ]
//            );
//
//        } catch (\Exception $e) {
//            Notification::make()
//                ->danger()
//                ->title('Failed to send WhatsApp Message')
//                ->body($e->getMessage())
//                ->send();
//        }
//    }



//    protected function sendViaTwilio(User $user, $settings)
//    {
//        $sid    = $settings['twilio_sid'] ?? null;
//        $token  = $settings['twilio_token'] ?? null;
//        $from   = $settings['twilio_whatsapp_from'] ?? null;
//
//        if (!$sid || !$token || !$from) {
//            Notification::make()
//                ->danger()
//                ->title('Missing Twilio Settings')
//                ->body('Ensure SID, Token, and From number are set.')
//                ->send();
//            return;
//        }
//
//        try {
//            $twilio = new Client($sid, $token);
//
//            $twilio->messages->create(
//                'whatsapp:' . $user->phone,
//                [
//                    'from' => $from,
//                    'body' => $this->message,
//                ]
//            );
//        } catch (\Exception $e) {
//            Notification::make()
//                ->danger()
//                ->title('WhatsApp Send Error')
//                ->body($e->getMessage())
//                ->send();
//        }
//    }



//    protected function sendDatabaseNotification(User $user)
//    {
//        $user->notify(new CustomAdminNotification($this->message));
//    }

}
