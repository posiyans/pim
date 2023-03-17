<?php

namespace App\Modules\Telegram\Classes;

use App\Models\User;
use GuzzleHttp\Client as HttpClient;

class TelegramDeleteLoginCodeMessage
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function run()
    {
        $opt = $this->user->options;
        if (isset($opt['telegram_login_message_id'])) {
            try {
                $telegram = new TelegramClass(
                    config('services.telegram-bot-api.token'),
                    app(HttpClient::class),
                    config('services.telegram-bot-api.base_uri')
                );
                $d = [
                    'chat_id' => $opt['telegram'],
                    'message_id' => $opt['telegram_login_message_id'],
                ];
                $telegram->deleteMessage($d);
            } catch (\Exception $e) {
            }
            unset($opt['telegram_login_message_id']);
            $this->user->options = $opt;
            $this->user->save();
        }
    }


}
