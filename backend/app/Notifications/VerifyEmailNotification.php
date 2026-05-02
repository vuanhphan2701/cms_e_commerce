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
     * Build the mail message.
     */
    public function toMail($notifiable): MailMessage
    {
        $otp = $notifiable->email_verification_otp;

        return (new MailMessage)
            ->subject('Mã xác thực email - E-commerce CMS')
            ->greeting('Xin chào ' . $notifiable->name . '!')
            ->line('Cảm ơn bạn đã đăng ký tài khoản tại E-commerce CMS.')
            ->line('Mã xác thực (OTP) của bạn là:')
            ->line(new \Illuminate\Support\HtmlString('<div style="font-size: 24px; font-weight: bold; letter-spacing: 5px; text-align: center; padding: 10px; background-color: #f3f4f6; border-radius: 5px; margin: 20px 0;">' . $otp . '</div>'))
            ->line('Mã này sẽ hết hạn sau 15 phút.')
            ->line('Nếu bạn không tạo tài khoản, vui lòng bỏ qua email này.')
            ->salutation('Trân trọng, E-commerce CMS');
    }
}
