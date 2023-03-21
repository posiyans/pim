<?php

namespace App\Modules\Telegram\Classes;

use App\Models\User;

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
                $telegram = new TelegramClass();
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
