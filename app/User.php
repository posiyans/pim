<?php

namespace App;

use App\Http\Models\UserToken;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;
use App\Http\Controllers\Sms\Sms;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'roles' => 'array',
        'aliases' => 'array',
    ];

    /**
     * Получить log модели
     */
    public function log()
    {
        return $this->morphMany('App\Http\Models\Log', 'commentable');
    }

    public function sendCodeSms(){
        if ($this->login_by_sms and env('SMS_API_ENABLE') and $this->phone) {
            $apikey=env('SMS_API_KEY');
            $smsru = new Sms($apikey);
            $data = new \stdClass();
            $data->to = $smsru->parserPhone($this->phone);
            $data->text = mt_rand(1000, 9999);
            $data->partner_id = env('SMS_PARTNER');
            if(env('SMS_TEST')) {
                $data->test = 1;
            }
            $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную
            if ($sms->status == "OK") {
                $this->sms = $data->text;
                $this->save();
                return ['status' => 'send', 'natation' => 'Sms отправлено на номер ' . $data->to . '/'. $data->text];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function createToken(Request $request, $len=64){
        $token = new UserToken($request);
        $token -> save();
        return $token;
    }

    public function d__hasRole($roles){

        if (is_array($roles)) {

            foreach ($roles as $role) {
                if (in_array($role,$this->roles)) {
                    return true;
                }
            }
        } else {
            if (in_array($roles,$this->roles)) {
                return true;
            }
        }
        return false;
    }


}
