<?php

namespace App\Modules\GlobalOptions\Controllers;

use App\Http\Controllers\MyController;
use App\Modules\GlobalOptions\Repositories\SettingRepository;

class GetMailSettingController extends MyController
{
    public function __construct()
    {
        $this->middleware('only_admin');
    }


    public function index()
    {
        $def = [
            'MAIL_HOST' => '',
            'MAIL_PORT' => 587,
            'MAIL_USERNAME' => '',
            'MAIL_PASSWORD' => '',
            'MAIL_ENCRYPTION' => 'tls',
        ];
        $opt = (new SettingRepository())->getOptionValue('mail_setting', $def);
//        print_r($opt);
        return response($opt);
    }

}
