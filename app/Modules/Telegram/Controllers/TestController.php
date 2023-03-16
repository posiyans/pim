<?php

namespace App\Modules\Telegram\Controllers;

use App\Http\Controllers\MyController;
use Illuminate\Http\Request;
use PragmaRX\Google2FA\Google2FA;

class TestController extends MyController
{


    public function index(Request $request)
    {
        $google2fa = new Google2FA();

        $secret = 'Q3URFWJTBB7MX55Y';


        return [
            $secret,
            $_GET['c'],
            $google2fa->verifyKey($secret, $_GET['c']),
            $google2fa->getQRCodeUrl(
                'pragmarx',
                'google2fa@pragmarx.com',
                $secret
            )
        ];
//        return $google2fa->generateSecretKey();
    }


}
