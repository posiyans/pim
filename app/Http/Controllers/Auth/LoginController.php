<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MyController;
use App\Modules\Log\Models\Log;
use App\Modules\User\Classes\CheckUserTwoFactorCodeClass;
use App\Modules\User\Classes\CreateUserTwoFactorCodeClass;
use App\Notifications\TwoFactorAuthentication;
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
        $login = trim($request->username);
        $password = $request->password;
        // нет смс проверям логин и пароль
        $credentials = ['login' => $login, 'password' => $password];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            if ($user->two_factor) {
                if ($request->code) {
                    try {
                        (new CheckUserTwoFactorCodeClass($user, $request->code))->run();
                    } catch (\Exception $e) {
                        return response(['status' => 'errorCode', 'error' => $e->getMessage()], 200);
                    }
                } else {
                    $code = (new CreateUserTwoFactorCodeClass($user))->run();
                    try {
                        $user->notify((new TwoFactorAuthentication($code)));
//                        event(new LogNotification());
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error($e->getMessage());
                    }
                    return response(['status' => 'sendCode'], 200);
                }
            }
//            $user = Auth::user();
            $log = new Log();
            $log->description = 'login by login, password';
            $log->type = 'ok';
            $user->log()->save($log);
            $user->last_connect = date('Y-m-d H:i:s');
            $user->save();
            $token = $user->createToken('primary');
            return response(['status' => 'done', 'token' => $token->plainTextToken, 'user' => $user]);
        }
        $log = new Log();
        $log->description = 'bad login or password';
        $log->value = $credentials;
        $log->type = 'alert';
        $log->save();
        return response(['error' => 'Не верный логин или пароль'], 401);
    }


}
