<?php

namespace App\Modules\User\Controllers;

use App\Http\Controllers\MyController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use PragmaRX\Google2FA\Google2FA;

class ChangeTwoFactorSecretKeyController extends MyController
{
    //
    public function index(Request $request)
    {
        $user = Auth::user();
        $password = $request->password;
        if ($password && Hash::check($password, $user->password)) {
            $google2fa = new Google2FA();
            $secret = $google2fa->generateSecretKey(32);
            $user->twofa_secret = $secret;
            $user->save();
            // todo log change
            return response(['secret' => $secret]);
        }
        return response(['error' => 'Неверный пароль'], 405);
    }


}
