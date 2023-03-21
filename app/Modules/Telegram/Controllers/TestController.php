<?php

namespace App\Modules\Telegram\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\Telegram\Classes\TelegramClass;
use GuzzleHttp\Client as HttpClient;
use Illuminate\Http\Request;

class TestController extends MyController
{

//    public function __construct()
//    {
//        $this->middleware('only_moderator');
//    }


    public function index(Request $request)
    {
        $telegram = new TelegramClass(
            config('services.telegram-bot-api.token'),
            app(HttpClient::class),
            config('services.telegram-bot-api.base_uri')
        );
        $data = [
            'chat_id' => 113737716,
            'text' => 'sdfsdfsdf'
        ];
//        $res = $this->sendRequest('sendMessage', $params);
        $res = $telegram->sendRaw('getMe', $data);
//        $res = $telegram->sendRaw('sendMessage', $data);
//        dd($res);
        return json_decode($res->getBody()->getContents(), true, 512, JSON_THROW_ON_ERROR);;
    }


}
