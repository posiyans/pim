<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MyController;
use App\Modules\Log\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends MyController
{
    //
    public function username()
    {
        return 'login';
    }

    public function index(Request $request)
    {
        $name = trim($request->username);
        $password = $request->password;
        // нет смс проверям логин и пароль
        $credentials = ['login' => $name, 'password' => $password];
        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $log = new Log();
            $log->description = 'login by login, password';
            $log->type = 'ok';
            $user->log()->save($log);
            $user->last_connect = date('Y-m-d H:i:s');
            $user->save();
            $token = $user->createToken('primary');
            return response(['token' => $token->plainTextToken, 'user' => $user]);
        }
        $log = new Log();
        $log->description = 'bad login or password';
        $log->value = $credentials;
        $log->type = 'alert';
        $log->save();
        return response(['error' => 'Не верный логин или пароль'], 401);
    }

}
