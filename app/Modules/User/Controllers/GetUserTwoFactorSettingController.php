<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
use App\Modules\GlobalOptions\Repositories\SettingRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GetUserTwoFactorSettingController extends MyController
{
    //
    public function index(Request $request)
    {
        $avtor = Auth::user();
        $id = $avtor->id;
        if ($avtor->moderator && $request->id) {
            $id = $request->id;
        }
        $user = User::find($id);
        $data = [
            'id' => $user->id,
            'two_factor' => $user->two_factor,
            'two_factor_enable' => $user->options['two_factor_enable'] ?? [],

            'two_factor_valid' => [
                [
                    'key' => 'google2fa',
                    'label' => 'Google Authenticator',
                    'error' => !$user->twofa_secret,
                    'error_message' => 'Не утановлен SecretKey',
                ]
            ]
        ];
        if ((new SettingRepository())->getOptionValue('two_factor_telegram_enable', false)) {
            $data['two_factor_valid'][] = [
                'key' => 'telegram',
                'label' => 'Telegram ',
                'error' => !$user->options['telegram'],
                'error_message' => 'Не указан id в Telegram',
            ];
        }
        if ((new SettingRepository())->getOptionValue('two_factor_mail_enable', false)) {
            $data['two_factor_valid'][] = [
                'key' => 'mail',
                'label' => 'E-mail',
                'error' => !$user->email,
                'error_message' => 'Не указан адрес почты',
            ];
        }
        return response($data);
    }


}
