<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramMessage;

class TwoFactorAuthentication extends Notification
{
    use Queueable;


    private $code;

    /**
     * Create a new notification instance.
     */
    public function __construct($code)
    {
        $this->code = $code;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $route = [];
        if (isset($notifiable->options['telegram']) && !empty($notifiable->options['telegram'])) {
            $route[] = 'telegram';
        }
        if ($notifiable->email) {
            $route[] = 'mail';
        }
        return $route;
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        return TelegramMessage::create()
            ->to($notifiable->options['telegram'])
            ->line("Код для входа")
            ->line($this->code);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Код для входа')
            ->greeting('Код для входа')
            ->action($this->code, '#')
            ->line('Для входа в систему введите данный код!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
