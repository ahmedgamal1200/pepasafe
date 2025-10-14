<?php

namespace App\Jobs;

use App\Models\ApiConfig;
use App\Models\AttendanceDocument;
use App\Models\Document;
use App\Models\Recipient;
use App\Models\User;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCertificateJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $certificate;

    public function __construct($certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * إرسال بريد إلكتروني باستخدام إعدادات SMTP من api_configs
     */
    protected function sendEmail($user, $message, $pdfPath)
    {
        try {
            $config = ApiConfig::whereIn('key', [
                'smtp_host', 'smtp_port', 'smtp_username', 'smtp_password',
                'smtp_from_address', 'smtp_from_name',
            ])->pluck('value', 'key')->toArray();

            if (empty($config['smtp_host']) || empty($config['smtp_from_address'])) {
                throw new Exception('إعدادات SMTP غير مكتملة.');
            }

            // إعداد الـ Mailer ديناميكيًا
            config([
                'mail.mailers.dynamic.transport' => 'smtp',
                'mail.mailers.dynamic.host' => $config['smtp_host'],
                'mail.mailers.dynamic.port' => $config['smtp_port'],
                'mail.mailers.dynamic.username' => $config['smtp_username'],
                'mail.mailers.dynamic.password' => $config['smtp_password'],
                'mail.mailers.dynamic.encryption' => 'tls',
                'mail.from.address' => $config['smtp_from_address'],
                'mail.from.name' => $config['smtp_from_name'] ?? 'Your App Name',
            ]);

            Mail::mailer('dynamic')->raw($message, function ($mail) use ($user, $pdfPath) {
                $mail->to($user->email)
                    ->subject('وثيقتك جاهزة!')
                    ->attach($pdfPath);
            });

            Log::info('Email sent to '.$user->email.' for certificate ID: '.$this->certificate->id);
        } catch (Exception $e) {
            Log::error('Error sending email for certificate ID '.$this->certificate->id.': '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * إرسال SMS باستخدام إعدادات SMS من api_configs
     * @throws ConnectionException
     */
    protected function sendSMS($phone, $message): void
    {
        $apiKey = 'OEklukhmcMiXQKrBS13UKHciPOFfWINIagSgZB0D4CTeoSx1h8OwrlR3FP9t' ?? null;
//        $to = $user->phone ?? null;
        $to = '+201205297854';

        $requestBody = [
            "name" => "BeOn Sales",
            "phoneNumber" => $to,
            "template_id" => 1651,
            "vars" => [
                $message
            ]
        ];

        $url = "https://v3.api.beon.chat/api/v3/messages/sms/template";

        try {
            $response = Http::withHeaders([
                'beon-token' => $apiKey,
            ])->post($url, $requestBody);

            if ($response->failed()) {
                throw new Exception('فشل إرسال SMS: '.$response->body());
            }

            Log::info('SMS sent to '.$phone.' for certificate ID: '.$this->certificate->id);
        } catch (Exception $e) {
            Log::error('Error sending SMS for certificate ID '.$this->certificate->id.': '.$e->getMessage());
            throw $e;
        }
    }

    /**
     * إرسال رسالة واتساب باستخدام إعدادات واتساب من api_configs
     * @throws ConnectionException
     */

    protected function sendWhatsApp($phone, $message): void
    {
        $sanitizedPhone = str_starts_with($phone, '+') ? $phone : "+{$phone}";

        try {
            $apiPayload = [
                "name"             => 'document_alert',
//                "phoneNumber"      => $sanitizedPhone, // تم التعديل لاستخدام متغير
                "phoneNumber"      => '+201205297854', // تم التعديل لاستخدام متغير
                "template_content" => 'test',
                "template_id"      => 2386,
                "workflow_id"      => 1,
                "template" => [
                    "name"     => "document_alert",
                    "language" => ["code" => "en"],
                    "components" => [
                        [
                            "type" => "body",
                            "parameters" => [
                                ["type" => "text", "text" => $message],
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
               Log::info('WhatsApp message sent successfully to '.$sanitizedPhone.' for certificate ID: '.$this->certificate->id);
            } else {
                Log::error('Failed to send WhatsApp message to '.$sanitizedPhone.' for certificate ID: '.$this->certificate->id.': '.$response->body());
            }
        } catch (Exception $e) {
            Log::error('Error sending WhatsApp message for certificate ID ' . ($this->certificate->id ?? 'N/A') . ': '.$e->getMessage());
            throw $e;
        }
    }

    public function handle(): void
    {
        try {
            // تحديد نوع الوثيقة (Document أو AttendanceDocument)
            $isAttendance = $this->certificate instanceof AttendanceDocument;

            // جلب الـ template من الـ certificate
            $template = $isAttendance ? $this->certificate->template : $this->certificate->template;

            // التحقق من وجود الـ template
            if (is_null($template)) {
                Log::error('Template not found for certificate ID: '.$this->certificate->id);
                throw new Exception('Template not found for certificate ID: '.$this->certificate->id);
            }

            $recipient = Recipient::find($this->certificate->recipient_id);
            $user = User::find($recipient->user_id);

            if (!$user || !$recipient) {
                throw new Exception('Recipient or User not found for certificate ID: '.$this->certificate->id);
            }

            // جلب طرق الإرسال والرسالة
            $sendVia = json_decode($template->send_via, true);
            $message = $template->message;

            // **الخطوة الجديدة: تحديد الـ Route ديناميكياً**
            $routeName = $isAttendance ? 'attendance.show' : 'documents.show';
            $certificateLink = route($routeName, $this->certificate->uuid);

            $fullMessage = $message . " You must log in using your email address and the default password 123456789, which should be changed immediately after logging in. " . $certificateLink;

            // مسار ملف الـ PDF
            $pdfPath = storage_path('app/public/'.$this->certificate->file_path);

            // إرسال عبر الطرق المختارة
            if (in_array('email', $sendVia)) {
                $this->sendEmail($user, $fullMessage, $pdfPath);
            }

            if (in_array('sms', $sendVia) && $user->phone) {
                $this->sendSMS($user->phone, $fullMessage);
            }

            if (in_array('whatsapp', $sendVia) && $user->phone) {
                $this->sendWhatsApp($user->phone, $fullMessage);
            }

            // تحديث حالة الوثيقة إلى "sent"
            $this->certificate->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

        } catch (Exception $e) {
            Log::error('Error sending certificate ID ' . ($this->certificate->id ?? 'N/A') . ': '.$e->getMessage());
            $this->fail($e);
        }
    }
}
