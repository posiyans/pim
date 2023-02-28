<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\MyController;
use App\Models\UserToken;
use App\Modules\Log\Models\Log;
use App\Modules\Sms\Classes\SendSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiAuthController extends MyController
{
    //
    public function username()
    {
        return 'login';
    }

    public function login(Request $request)
    {
        $name = trim($request->username);
        $password = $request->password;
        $sms = $request->sms;
        if (isset($sms)) {
            // проверка логин пароля и смс
            $credentials = ['login' => $name, 'sms' => $sms, 'password' => $password];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                Auth::login($user, true);
                $user->sms = null;
                $log = new Log();
                $log->description = 'login by login, password, sms';
                $log->type = 'ok';
                $user->log()->save($log);
                $user->save();
                $token = $user->createToken('primary');
                return $this->response(['token' => $token->plainTextToken, 'user' => $user]);
            } else {
                $log = new Log();
                $log->description = 'bad_sms';
                $credentials['password'] = '******';
                $log->value = $credentials;
                $log->type = 'alert';
                $log->save();
                return $this->response(['sms' => 'bad_sms']);
            }
        } else {
            // нет смс проверям логин и пароль
            $credentials = ['login' => $name, 'password' => $password];
            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                if (Auth::user()->login_by_sms) {
                    try {
                        $sms = (new SendSms($user))->code()->run();
                        $user->sms = $sms;
                        $user->save();
                        return $this->response(['sms' => 'send', 't' => $sms]);
                    } catch (\Exception $e) {
                        return $this->response(['error' => $e->getMessage()], 401);
                    }
                }
//                $token = $request->header('u-token');
//                $userToken = UserToken::where('u_token', $token)->first();

//                if (!isset($userToken)) {
//                    // нет токена отпрявляем смс
////                        if (Auth::user()->login_by_sms) {
////                            $sms = Auth::user()->sendCodeSms();
////                            return $this->response(['sms' => $sms]);
////                        }
//                    if ($sms = Auth::user()->sendCodeSms()) {
//                        return $this->response(['sms' => $sms]);
//                    }
//                } else {
//                    $userToken->delete();
//                }
                // если есть дествующий токен, пересоздаем токен и  авторизуем
                $log = new Log();
                $log->description = 'login by login, password';
                $log->type = 'ok';
                $user->log()->save($log);
                Auth::login($user, true);
                $token = $user->createToken('primary');
                return $this->response(['token' => $token->plainTextToken, 'user' => $user]);
            }
        }
        $log = new Log();
        $log->description = 'bad login or password';
        $log->value = $credentials;
        $log->type = 'alert';
        $log->save();
        return $this->response(['error' => 'Не верный логин или пароль'], 401);
    }

    public function logout(Request $request)
    {
        $token = $request->header('s-token');
        $userToken = UserToken::where('s_token', $token)->first();
        if ($userToken) {
            $userToken->s_token = null;
            $userToken->save();
            $log = new Log();
            $log->type = 'ok';
            $log->description = 'logout';
            $userToken->user->log()->save($log);
            Auth::logout();
        }
        //$userToken->runSoftDelete();
        return $this->response('');
    }
}
