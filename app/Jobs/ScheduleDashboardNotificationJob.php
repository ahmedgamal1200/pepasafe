<?php

namespace App\Jobs;

use App\Models\ApiConfig;
use App\Models\ScheduledNotification;
use App\Models\User;
use App\Notifications\CustomAdminNotification;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class ScheduleDashboardNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $scheduledNotificationId,
        public string $channel,
        public array $message,
        public ?string $subject = null,
        public array $userIds = [],
    ) {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $scheduledNotification = ScheduledNotification::find($this->scheduledNotificationId);
        if (! $scheduledNotification) {
            return; // في حالة ما لقاش السجل
        }

        $users = empty($this->userIds)
            ? User::all()
            : User::whereIn('id', $this->userIds)->get();

        $settings = ApiConfig::pluck('value', 'key');

        foreach ($users as $user) {
            $locale = app()->getLocale();
            $messageForUser = $this->message[$locale] ?? null;
            $subjectForUser = $this->subject; // الموضوع ليس مترجماً

            if (is_null($messageForUser)) {
                $messageForUser = $this->message['en'] ?? $this->message['ar'] ?? null;
            }
            match ($this->channel) {
                'email' => $this->sendEmail($user, $settings),
                'sms' => $this->sendSms($user, $settings),
                'whatsapp' => $this->sendWhatsApp($user, $settings),
                'database' => $user->notify(new CustomAdminNotification($messageForUser)),
                default => null,
            };
        }

        $scheduledNotification->update(['status' => 'sent', 'sent_at' => now()]);
    }

    protected function sendEmail(User $user, ?string $messageContent, $subjectContent): void
    {
        $settings = ApiConfig::whereIn('key', [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ])->pluck('value', 'key');

        $requiredKeys = [
            'mail_mailer',
            'mail_host',
            'mail_port',
            'mail_username',
            'mail_password',
            'mail_encryption',
            'mail_from_address',
            'mail_from_name',
        ];

        foreach ($requiredKeys as $key) {
            if (empty($settings[$key])) {
                Notification::make()
                    ->danger()
                    ->title('Missing Email Config')
                    ->body("Missing config: $key")
                    ->send();

                return;
            }
        }

        // ضبط الإعدادات مؤقتًا
        config([
            'mail.default' => $settings['mail_mailer'],
            'mail.mailers.smtp.transport' => $settings['mail_mailer'],
            'mail.mailers.smtp.host' => $settings['mail_host'],
            'mail.mailers.smtp.port' => (int) $settings['mail_port'],
            'mail.mailers.smtp.encryption' => $settings['mail_encryption'],
            'mail.mailers.smtp.username' => $settings['mail_username'],
            'mail.mailers.smtp.password' => $settings['mail_password'],
            'mail.from.address' => $settings['mail_from_address'],
            'mail.from.name' => $settings['mail_from_name'],
        ]);

        try {
            Mail::raw($messageContent, function ($message) use ($user, $subjectContent) {
                $message->to($user->email)
                    ->subject($subjectContent ?: 'Message Form PepaSafe');
            });

            Notification::make()
                ->success()
                ->title('Email sent successfully!')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Email failed')
                ->body($e->getMessage())
                ->send();
        }
    }

    protected function sendSms(User $user, ?string $messageContent): void
    {
        $settings = ApiConfig::whereIn('key', [
            'sms_api_key',          // Twilio SID
            'sms_api_secret',       // Twilio Token
            'sms_sender_number',    // Twilio sender number (رقم الشراء من Twilio)
        ])->pluck('value', 'key');

        $sid = $settings['sms_api_key'] ?? null;
        $token = $settings['sms_api_secret'] ?? null;
        $from = $settings['sms_sender_number'] ?? null;
        $to = $user->phone ?? null;
        //        $to      = '+201279678444';

        if (! $sid || ! $token || ! $from || ! $to) {
            Notification::make()
                ->danger()
                ->title('Missing SMS Configuration')
                ->body('Please make sure SID, Token, Sender, and User Phone are set correctly.')
                ->send();

            return;
        }

        try {
            $twilio = new \Twilio\Rest\Client($sid, $token);

            $twilio->messages->create(
                $to, // الرقم المستلم بصيغة دولية
                [
                    'from' => $from,
                    'body' => $messageContent,
                ]
            );

            Notification::make()
                ->success()
                ->title('SMS sent successfully!')
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('SMS Sending Failed')
                ->body($e->getMessage())
                ->send();
        }
    }

//    protected function sendWhatsApp(User $user, ?string $messageContent): void
//    {
//        // جلب الإعدادات من قاعدة البيانات حسب الأسماء الجديدة
//        $settings = ApiConfig::whereIn('key', [
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
//
//            return;
//        }
//
//        try {
//            $sid = $settings['whatsapp_api_key'];
//            $token = $settings['whatsapp_api_secret'];
//            $from = $settings['whatsapp_phone_number'];
//
//            $twilio = new \Twilio\Rest\Client($sid, $token);
//
//            $twilio->messages->create(
//                'whatsapp:'.$user->phone,
//                [
//                    'from' => 'whatsapp:'.$from,
//                    'body' => $messageContent,
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


    protected function sendWhatsApp(User $user, ?string $messageContent): void
    {
        try {
            $response = Http::withHeaders([
                'beon-token' => 'OEklukhmcMiXQKrBS13UKHciPOFfWINIagSgZB0D4CTeoSx1h8OwrlR3FP9t',
                'Accept'        => 'application/json',
            ])->post('https://v3.api.beon.chat/api/v3/messages/whatsapp/template', [ // شوف الـ endpoint الصح من Postman Docs
//                'to'      => $user->phone,         // رقم المرسل له
                "name"             => $user->name,
                "phoneNumber"      => '+201205297854',      // لازم بصيغة دولية
                "template_content" => $messageContent,   // النص اللي هيظهر
                "template_id"      => 274,               // لازم تجيبه من حسابك
                "workflow_id"      => 1,                 // حسب إعداداتك
                "template" => [
                    "name"     => "template_name",       // اسم التيمبلت اللي متسجل عندهم
                    "language" => ["code" => "ar"],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => "100"],
                                ["type" => "text", "text" => "منتج ١"],
                                ["type" => "text", "text" => "100 جنيه"],
                            ]
                        ]
                    ]
                ]      // محتوى الرسالة
            ]);

            if ($response->successful()) {
                Notification::make()
                    ->success()
                    ->title('Message Sent')
                    ->body('WhatsApp message sent successfully.')
                    ->send();
            } else {
                Notification::make()
                    ->danger()
                    ->title('Failed to send WhatsApp Message')
                    ->body('API Error: '.$response->body())
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Failed to send WhatsApp Message')
                ->body($e->getMessage())
                ->send();
        }
    }
}
