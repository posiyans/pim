<?php

namespace App\Http\Models;

use App\MyModel;
use Illuminate\Support\Facades\Auth;

class UserToken extends MyModel
{

    protected $dates = ['deleted_at'];

    protected $casts = [
        'info' => 'array',
    ];

    public function user()
    {
        return $this->hasOne('App\User','id', 'user_id');
    }

    public function __construct($request=false, array $attributes = [])
    {

        parent::__construct($attributes);
        $this->user_id = Auth::id();
        $this->u_token = $this->randomString();
        $this->s_token = $this->randomString();
        $this->time_live= date("Y-m-d H:i:s", strtotime("+1 month"));
        if ($request) {
            $this->info = [$request->ip(), $request->header('User-Agent')];
        }
    }

    public static function randomString($len=64)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $randstring = '';
        for ($i = 0; $i < $len; $i++) {
            $randstring .= $characters[rand(0, strlen($characters) - 1)];
        }
        return $randstring;
    }

    public function ReCreateSesionToken(){
        $this->s_token = $this->randomString();
        $this->save();
        return ['user'=>$this->u_token, 'sesions'=> $this->s_token];
    }

    public function sendAllTokens(){
        return ['user'=>$this->u_token, 'sesions'=> $this->s_token];

    }
}
