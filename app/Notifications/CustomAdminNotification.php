<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class CustomAdminNotification extends Notification
{
    use Queueable;

    public function __construct(public string $messageText)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database']; // أو ['mail', 'database']
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line($this->messageText)
            ->action('اذهب إلى الموقع', url('/'))
            ->line('شكرًا لاستخدامك نظامنا.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'message' => $this->messageText,
        ];
    }
}

