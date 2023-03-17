<?php

namespace App\Listeners;


use Illuminate\Notifications\Events\NotificationSent;

class LogNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NotificationSent $event): void
    {
        if (get_class($event->notification) == 'App\Notifications\TwoFactorAuthentication' &&
            $event->channel == 'telegram') {
            $opt = $event->notifiable->options;
            $opt['telegram_login_message_id'] = $event->response['result']['message_id'];
            $event->notifiable->options = $opt;
            $event->notifiable->save();
        }
    }
}
