<?php

namespace App\Modules\Telegram\Classes;

use GuzzleHttp\Client as HttpClient;

class TelegramMyInfo
{
    public function run()
    {
        try {
            $telegram = new TelegramClass(
                config('services.telegram-bot-api.token'),
                app(HttpClient::class),
                config('services.telegram-bot-api.base_uri')
            );
            $res = $telegram->sendRaw('getMe', []);
            $content = json_decode($res->getBody()->getContents());
            if ($content->ok) {
                return $content->result;
            }
            return false;
        } catch (\Exception $e) {
        }
        return false;
    }
}
