<?php

namespace App\Jobs;

use App\Models\AttendanceDocument;
use App\Models\Document;
use App\Models\Recipient;
use App\Models\User;
use App\Models\ApiConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

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
                'smtp_from_address', 'smtp_from_name'
            ])->pluck('value', 'key')->toArray();

            if (empty($config['smtp_host']) || empty($config['smtp_from_address'])) {
                throw new \Exception('إعدادات SMTP غير مكتملة.');
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

            Log::info('Email sent to ' . $user->email . ' for certificate ID: ' . $this->certificate->id);
        } catch (\Exception $e) {
            Log::error('Error sending email for certificate ID ' . $this->certificate->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إرسال SMS باستخدام إعدادات SMS من api_configs
     */
    protected function sendSMS($phone, $message)
    {
        try {
            $config = ApiConfig::whereIn('key', ['sms_api_key', 'sms_sender_id'])->pluck('value', 'key')->toArray();

            if (empty($config['sms_api_key']) || empty($config['sms_sender_id'])) {
                throw new \Exception('إعدادات SMS غير مكتملة.');
            }

            // مثال باستخدام Twilio أو API مشابه
            $response = Http::withBasicAuth($config['sms_api_key'], '')->post('https://api.twilio.com/2010-04-01/Accounts/' . $config['sms_api_key'] . '/Messages.json', [
                'From' => $config['sms_sender_id'],
                'To' => $phone,
                'Body' => $message,
            ]);

            if ($response->failed()) {
                throw new \Exception('فشل إرسال SMS: ' . $response->body());
            }

            Log::info('SMS sent to ' . $phone . ' for certificate ID: ' . $this->certificate->id);
        } catch (\Exception $e) {
            Log::error('Error sending SMS for certificate ID ' . $this->certificate->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * إرسال رسالة واتساب باستخدام إعدادات واتساب من api_configs
     */
    protected function sendWhatsApp($phone, $message)
    {
        try {
            $config = ApiConfig::whereIn('key', ['whatsapp_api_key', 'whatsapp_api_secret', 'whatsapp_phone_number'])->pluck('value', 'key')->toArray();

            if (empty($config['whatsapp_api_key']) || empty($config['whatsapp_phone_number'])) {
                throw new \Exception('إعدادات واتساب غير مكتملة.');
            }

            // مثال باستخدام Twilio WhatsApp API
            $response = Http::withBasicAuth($config['whatsapp_api_key'], $config['whatsapp_api_secret'])->post('https://api.twilio.com/2010-04-01/Accounts/' . $config['whatsapp_api_key'] . '/Messages.json', [
                'From' => 'whatsapp:' . $config['whatsapp_phone_number'],
                'To' => 'whatsapp:' . $phone,
                'Body' => $message,
            ]);

            if ($response->failed()) {
                throw new \Exception('فشل إرسال رسالة واتساب: ' . $response->body());
            }

            Log::info('WhatsApp message sent to ' . $phone . ' for certificate ID: ' . $this->certificate->id);
        } catch (\Exception $e) {
            Log::error('Error sending WhatsApp message for certificate ID ' . $this->certificate->id . ': ' . $e->getMessage());
            throw $e;
        }
    }

    public function handle()
    {
        try {
            // تحديد نوع الوثيقة (Document أو AttendanceDocument)
            $isAttendance = $this->certificate instanceof AttendanceDocument;
            $template = $isAttendance ? $this->certificate->attendanceTemplate : $this->certificate->documentTemplate;
            $recipient = Recipient::find($this->certificate->recipient_id);
            $user = User::find($recipient->user_id);

            if (!$user || !$recipient) {
                throw new \Exception('Recipient or User not found for certificate ID: ' . $this->certificate->id);
            }

            // جلب طرق الإرسال والرسالة
            $sendVia = json_decode($template->send_via, true);
            $message = $template->message;
            $certificateLink = route('certificate.show', $this->certificate->uuid);
            $fullMessage = $message . "\nرابط الوثيقة: " . $certificateLink;

            // مسار ملف الـ PDF
            $pdfPath = storage_path('app/public/' . $this->certificate->file_path);

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

        } catch (\Exception $e) {
            Log::error('Error sending certificate ID ' . $this->certificate->id . ': ' . $e->getMessage());
            $this->fail($e);
        }
    }
}
