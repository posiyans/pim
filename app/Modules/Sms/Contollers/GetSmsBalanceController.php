<?php

namespace App\Modules\Sms\Contollers;

use App\Http\Controllers\MyController;
use App\Modules\Sms\Classes\SendSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetSmsBalanceController extends MyController
{

    public function index(Request $request)
    {
        $user = Auth::user();
        if ($user->moderator) {
            return SendSms::getBalance();
        }

        return '';
    }


}
