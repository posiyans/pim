<?php

namespace App\Notifications;

use App\Modules\Telegram\Classes\TelegramMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

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
        if ($notifiable->email && in_array('mail', $notifiable->options['two_factor_enable'])) {
            $route[] = 'mail';
        }
        if (isset($notifiable->options['telegram']) &&
            !empty($notifiable->options['telegram']) &&
            in_array('telegram', $notifiable->options['two_factor_enable'])
        ) {
            $route[] = 'telegram';
        }
        return $route;
    }


    /**
     * Get the mail representation of the notification.
     */
    public function toTelegram(object $notifiable)
    {
        return (new TelegramMessage())
            ->to($notifiable->options['telegram'])
            ->line($this->code)
            ->line("Код для входа");
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        Log::error("mail");
        Log::error($this->code);
        return (new MailMessage)
            ->subject('Код для входа ' . $this->code)
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
