<?php

namespace App\Modules\GlobalOptions\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\GlobalOptions\Classes\UpdateSetting;
use App\Modules\GlobalOptions\Models\Setting;
use App\Modules\GlobalOptions\Repositories\SettingRepository;
use Illuminate\Http\Request;

class ChangeTwoFactorEnableController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_admin');
    }


    public function index(Request $request)
    {
        $name = $request->name;
        $value = $request->value;

        $ar = [
            'telegram' => 'two_factor_telegram_enable',
            'mail' => 'two_factor_mail_enable'
        ];
        if (isset($ar[$name])) {
            $opt = (new SettingRepository())->getOption($ar[$name]);
            if (!$opt) {
                $opt = new Setting();
                $opt->type = 'boolean';
                $opt->field = $ar[$name];
            }
            $opt = (new UpdateSetting($opt))->value($value)->update();
            return response($opt);
        }
    }

}
