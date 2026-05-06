<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends Notification
{
    use Queueable;

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Build the verification URL.
     */
    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }

    /**
     * Build the mail message.
     * ✏️ EDIT THE CONTENT BELOW TO CUSTOMIZE YOUR EMAIL
     */
    public function toMail($notifiable): MailMessage
    {
        $url = $this->verificationUrl($notifiable);

        return (new MailMessage)
            ->subject('Xác thực email - E-commerce CMS')
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Cảm ơn bạn đã đăng ký tài khoản tại E-commerce CMS.')
            ->line('Vui lòng nhấn nút bên dưới để xác thực địa chỉ email của bạn.')
            ->action('Xác Thực Email', $url)
            ->line('Liên kết này sẽ hết hạn sau 60 phút.')
            ->line('Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.')
            ->salutation('Trân trọng, E-commerce CMS');
    }
}
