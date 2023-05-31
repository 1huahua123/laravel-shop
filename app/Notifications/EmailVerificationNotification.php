<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;

class EmailVerificationNotification extends Notification
{
    use Queueable;

    /**
     * 我们只需要通过邮件通知，因此这里只需要一个 mail 即可
     * @param $notifiable
     * @return string[]
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * 发送邮件时会调用此方法来构建邮件内容，参数就是 App\Models\User 对象
     * @param $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        // 使用内置的Str类生成随机字符串，字符串长度为16
        $token = Str::random(16);
        // 设置一个key为email_verification_email的缓存，值为随机字符串，有效时间30分钟
        Cache::set('email_verification_'.$notifiable->email, $token, 30);
        $url = route('email_verification.verify', ['email' => $notifiable->email, 'token' => $token]);
        return (new MailMessage)
                    ->greeting($notifiable->name.'您好：')
                    ->subject('注册成功，请验证您的邮箱')
                    ->line('请点击下方链接验证您的邮箱')
                    ->action('验证', $url);
    }

    /**
     * @param $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }

}
