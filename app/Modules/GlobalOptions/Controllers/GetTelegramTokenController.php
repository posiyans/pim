<?php

namespace App\Modules\GlobalOptions\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\GlobalOptions\Repositories\SettingRepository;

class GetTelegramTokenController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_admin');
    }


    public function index()
    {
        $opt = (new SettingRepository())->getOptionValue('telegram_token', '');
        return response($opt);
    }

}
