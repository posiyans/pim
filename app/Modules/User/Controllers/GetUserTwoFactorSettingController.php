<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use App\Models\User;
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
                'mail' => !!$user->email,
                'telegram' => !!$user->options['telegram'],
                'google2fa' => !!$user->twofa_secret,
            ]
        ];
        return response($data);
    }


}
