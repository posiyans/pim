<?php

namespace App\Modules\Telegram\Controllers;

use App\Http\Controllers\MyController;
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
        return (new TelegramMyInfo())->run();
    }


}
