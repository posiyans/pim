<?php

namespace App\Modules\GlobalOptions\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\GlobalOptions\Classes\UpdateSetting;
use App\Modules\GlobalOptions\Models\Setting;
use App\Modules\GlobalOptions\Repositories\SettingRepository;
use Illuminate\Http\Request;

class UpdateMailSettingController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_admin');
    }


    public function index(Request $request)
    {
        $data = $request->mail;
        $opt = (new SettingRepository())->getOption('mail_setting');
        if (!$opt) {
            $opt = new Setting();
            $opt->type = 'array';
            $opt->field = 'mail_setting';
        }
        $opt = (new UpdateSetting($opt))->value($data)->update();
        return response($opt);
    }

}
