<?php

namespace App\Modules\GlobalOptions\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\GlobalOptions\Classes\UpdateSetting;
use App\Modules\GlobalOptions\Models\Setting;
use App\Modules\GlobalOptions\Repositories\SettingRepository;
use Illuminate\Http\Request;

class UpdateTelegramTokenController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_admin');
    }


    public function index(Request $request)
    {
        $token = $request->token;
        $opt = (new SettingRepository())->getOption('telegram_token');
        if (!$opt) {
            $opt = new Setting();
            $opt->type = 'password';
            $opt->field = 'telegram_token';
        }
        $opt = (new UpdateSetting($opt))->value($token)->update();
        return response($opt);
    }

}
