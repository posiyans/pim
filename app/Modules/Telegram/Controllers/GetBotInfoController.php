<?php

namespace App\Modules\Telegram\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\GlobalOptions\Repositories\SettingRepository;
use App\Modules\Telegram\Classes\TelegramMyInfo;
use Illuminate\Http\Request;

class GetBotInfoController extends MyController
{

    public function __construct()
    {
        $this->middleware('only_moderator');
    }


    public function index(Request $request)
    {
        $bot = (new TelegramMyInfo())->run();
        $bot->two_factor_telegram = (new SettingRepository())->getOptionValue('two_factor_telegram_enable', false);
        return $bot;
    }


}
