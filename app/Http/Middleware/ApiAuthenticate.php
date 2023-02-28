<?php

namespace App\Http\Middleware;
use App\Models\UserToken;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class ApiAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

//        if ($request->method() == 'OPTIONS'){
//            return $this->response('api');
//        }
        //
        if ($request->path() == 'api/login/login'){
            return $next($request);
        }
//        if ($request->path() == 'api/user/avatar/15'){
//            return $next($request);
//        }
        $token=$request->header('s-token');
        if (!$token){
            $token = Cookie::get('S-Token');
        }
        if (strlen($token) > 40) {
            $userToken = UserToken::where('s_token', $token)->first();
        }

        if (isset($userToken)){
            Auth::login($userToken->user);
            return $next($request);
        }
        //return $next($request);
        return $this->response('api', 401);
    }

    protected function response($var, $code=200){
        return response($var, $code)->header('Access-Control-Allow-Origin','*')->header('Access-Control-Allow-Headers','Origin, X-Requested-With, Content-Type, Accept, S-Token, U-Token ')->header('Access-Control-Allow-Methods','GET,HEAD,OPTIONS,POST,PUT');
    }
}
