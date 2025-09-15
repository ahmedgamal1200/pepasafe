<?php

namespace App\Jobs;

use App\Models\ApiConfig;
use App\Models\ScheduledNotification;
use App\Models\User;
use App\Notifications\CustomAdminNotification;
use Filament\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
                'email' => $this->sendEmail($user, $messageForUser, $subjectForUser),
                'sms' => $this->sendSms($user, $messageForUser, ),
                'whatsapp' => $this->sendWhatsApp($user, $messageForUser),
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



    protected function sendSms(User $user, ?string $messageContent): array
    {
        $apiKey = 'OEklukhmcMiXQKrBS13UKHciPOFfWINIagSgZB0D4CTeoSx1h8OwrlR3FP9t' ?? null;
        $to = $user->phone ?? null;

        $requestBody = [
            "name" => "BeOn Sales",
            "phoneNumber" => $to,
            "template_id" => 1651,
            "vars" => [
                $messageContent
            ]
        ];

        $url = "https://v3.api.beon.chat/api/v3/messages/sms/template";

        try {
            $response = Http::withHeaders([
                'beon-token' => $apiKey,
            ])->post($url, $requestBody);


            if ($response->successful()) {
                Notification::make()
                    ->success()
                    ->title('تم إرسال الرسالة القصيرة بنجاح!')
                    ->send();
                return ['success' => 'send', 'code' => 200];
            } else {
                // استخدام status() و body() للحصول على تفاصيل الخطأ من الاستجابة
                $errorBody = json_decode($response->body(), true) ?? ['message' => 'استجابة غير صالحة'];
                $errorMessage = $errorBody['message'] ?? 'فشل إرسال الرسالة';

                Notification::make()
                    ->danger()
                    ->title('فشل إرسال الرسالة القصيرة')
                    ->body($response->status() . ': ' . $errorMessage)
                    ->send();

                return ['error' => $errorMessage, 'code' => $response->status()];
            }
        } catch (\Exception $e) {
            // تسجيل الاستثناء في السجل
            Log::error('BeOn API Request Failed:', ['exception' => $e->getMessage()]);

            Notification::make()
                ->danger()
                ->title('فشل إرسال الرسالة القصيرة')
                ->body('حدث خطأ غير متوقع: ' . $e->getMessage())
                ->send();

            return ['error' => 'Exception: ' . $e->getMessage(), 'code' => 500];
        }
    }


    protected function sendWhatsApp(User $user, ?string $messageContent): void
    {
        // *** الخطوة 1: Logging لبداية العملية ***
        Log::info('WhatsApp Job: Attempting to send message.', [
            'user_id' => $user->id,
            'user_phone_from_db' => $user->phone,
            'message_content_excerpt' => substr($messageContent ?? 'N/A', 0, 50),
        ]);

        $targetPhoneNumber = $user->phone;



        try {
            $apiPayload = [
                "name"             => 'gouda',
                "phoneNumber"      => $targetPhoneNumber, // تم التعديل لاستخدام متغير
                "template_content" => 'test',
                "template_id"      => 1492,
                "workflow_id"      => 1,
                "template" => [
                    "name"     => "pepasafe_test",
                    "language" => ["code" => "ar"],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => $messageContent],
                            ]
                        ]
                    ]
                ]
            ];

            // *** الخطوة 2: إضافة Timeout وإرسال الطلب ***
            $response = Http::timeout(30)->withHeaders([
                'beon-token' => 'OEklukhmcMiXQKrBS13UKHciPOFfWINIagSgZB0D4CTeoSx1h8OwrlR3FP9t',
                'Accept'     => 'application/json',
            ])->post('https://v3.api.beon.chat/api/v3/messages/whatsapp/template', $apiPayload);

            // *** الخطوة 3: Logging لنتيجة الـ API ***
            Log::info('WhatsApp Job: API Response.', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'response_body' => $response->body(),
            ]);

            if ($response->successful()) {
                Notification::make()
                    ->success()
                    ->title('Message Sent')
                    ->body('WhatsApp message sent successfully.')
                    ->send();
            } else {
                // *** الخطوة 4: التعامل مع فشل الـ API (رسالة خطأ من المزود) ***
                Notification::make()
                    ->danger()
                    ->title('Failed to send WhatsApp Message (API Error)')
                    ->body("API Status: {$response->status()} | Details: ".$response->body())
                    ->send();
            }
        } catch (\Exception $e) {
            // *** الخطوة 5: التعامل مع فشل الاتصال (Localhost, Network, الخ.) ***
            Log::error('WhatsApp Job: Connection Exception.', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title('Failed to send WhatsApp Message (Connection Error)')
                ->body('Connection Error: '.$e->getMessage())
                ->send();
        }
    }
}
