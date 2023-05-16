<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MyController;
use App\Models\User;
use App\Modules\User\Classes\SendTwoFactorCodeClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CreateUserController extends MyController
{

    public function index(Request $request)
    {
        $user = User::first();
        if ($user) {
            return response('', 404);
        } else {
            $user = new User();
            $user->login = $request->username;
            $user->name = $request->username;
            $user->full_name = $request->username;
            $user->roles = ['user'];
            $user->admin = true;
            $user->hide = true;
            $user->moderator = true;
            $user->two_factor = false;
            $user->aliases = [];
            $user->password = Hash::make($request->password);
            $opt = [
                'phone' => '',
                'telegram' => ''
            ];
            $user->options = $opt;
            if ($user->save()) {
                return response('');
            }
        }
    }


}
