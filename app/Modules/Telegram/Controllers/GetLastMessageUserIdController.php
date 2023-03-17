<?php

namespace App\Modules\Telegram\Controllers;

use App\Http\Controllers\MyController;
use Illuminate\Http\Request;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Telegram\TelegramUpdates;

class GetLastMessageUserIdController extends MyController
{

    public function __construct()
    {
        $this->middleware('only_moderator');
    }


    public function index(Request $request)
    {
        $updates = TelegramUpdates::create()
            ->latest()

            ->options([
                'timeout' => 0,
            ])
            ->get();
        if ($updates['ok']) {
            $userTelegram = $updates['result'][0]['message']['from'];
            TelegramMessage::create()
                ->to($userTelegram['id'])
                ->line("Ваш id:")
                ->line($userTelegram['id'])
                ->send();
            return $userTelegram;
        }
    }


}
