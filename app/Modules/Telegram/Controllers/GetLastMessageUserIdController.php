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
//        $user = User::find(15);
//        $code = rand(1000, 9999);
//        $user->notify(
//            (new TwoFactorAuthentication($code))
//        );
//                if ($updates['ok']) {
////            // Chat ID
////            $chatId = $updates['result'][0]['message']['chat']['id'];
//            $chatId = $updates['result'][0]['message']['chat']['id'];
//            dump($chatId);
//            dump($updates['result'][0]['message']['text']);
//        }

//        Notification::route('telegram')->notify(new TwoFactorAuthentication($code));

//        Mail::to('posiyans@yandex.ru')->send(
//            (new MailMessage)
//                ->line('The introduction to the notification.')
//                ->action($code, '/')
//                ->line('Thank you for using our application!')
//
//        );
////        Mail::raw('posiyans@yandex.ru', function ($message) {
//            $message->to('posiyans@yandex.ru');
//            $message->subject('5% off all our website');
//        });
////        $updates = TelegramUpdates::create()
////            // (Optional). Get's the latest update. NOTE: All previous updates will be forgotten using this method.
////            ->latest()
////
////            // (Optional). Limit to 2 updates (By default, updates starting with the earliest unconfirmed update are returned).
////            ->limit(2)
////
////            // (Optional). Add more params to the request.
////            ->options([
////                'timeout' => 0,
////            ])
////            ->get();
////
////        dump($updates);
////        if ($updates['ok']) {
//////            // Chat ID
//////            $chatId = $updates['result'][0]['message']['chat']['id'];
////            $chatId = $updates['result'][0]['message']['chat']['id'];
////            dump($chatId);
////            dump($updates['result'][0]['message']['text']);
////        }
//        $code = rand(1000, 9999);
//        $s = TelegramMessage::create('201117732:AAFfw7xQ77jL6NmxlQj-xcHc42U6O3K5GEQ');
////        $s->setToken('201117732:AAFfw7xQ77jL6NmxlQj-xcHc42U6O3K5GEQ');
//        // Optional recipient user id.
//        $s->to(113737716)
//            // Markdown supported.
////            ->content("Hello there!\nrdgetteqt\n")
//            ->line("Код для входа")
//            ->line($code)
//
//            // (Optional) Blade template for the content.
//            // ->view('notification', ['url' => $url])
//
//            // (Optional) Inline Buttons
//            // (Optional) Inline Button with callback. You can handle callback in your bot instance
//            ->send();
//        dump($s);
////        TelegramPoll::create()
////            ->to(113737716)
////            ->question("Aren't Laravel Notification Channels awesome?")
////            ->choices(['Yes', 'YEs', 'YES'])
////            ->send();
//        return 'sdfsdfs';
    }


}
