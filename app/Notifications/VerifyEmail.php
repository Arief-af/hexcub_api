<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Alamat Email')
            ->line('Klik tombol di bawah ini untuk memverifikasi alamat email Anda.')
            ->action('Verifikasi Alamat Email', $this->verificationUrl($notifiable));
    }

    /**
     * Get the verification URL for the notification.
     */
    protected function verificationUrl($notifiable)
    {
        // Membuat Signed URL untuk verifikasi email
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['email' => $notifiable->getEmailForVerification(), 'hash' => sha1($notifiable->email)]
        );
    
        // Kembalikan URL frontend dengan signed URL tanpa encode tambahan
        return  'https://hexcub.zqdevs.my.id/verify-email?' . parse_url($signedUrl, PHP_URL_QUERY);
    }
    

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [];
    }
}
