<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles, HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'login',
        'phone',
        'email',
        'full_name',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'roles' => 'array',
        'aliases' => 'array',
        'moderator' => 'boolean',
        'hide' => 'boolean',
        'login_by_sms' => 'boolean',
    ];


    /**
     * Получить log модели
     */
    public function log()
    {
        return $this->morphMany('App\Modules\Log\Models\Log', 'commentable');
    }

    public function sendCodeSms()
    {
        if ($this->login_by_sms and env('SMS_API_ENABLE') and $this->phone) {
            $apikey = env('SMS_API_KEY');
            $smsru = new Sms($apikey);
            $data = new \stdClass();
            $data->to = $smsru->parserPhone($this->phone);
            $data->text = mt_rand(1000, 9999);
            $data->partner_id = 2316;
            if (env('SMS_TEST')) {
                $data->test = 1;
            }
            $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную
            if ($sms->status == "OK") {
                $this->sms = $data->text;
                $this->save();
                if (env('SMS_TEST')) {
                    return ['status' => 'send', 'natation' => 'Sms отправлено на номер ' . $data->to . '/' . $data->text];
                } else {
                    return ['status' => 'send', 'natation' => 'Sms отправлено на номер ' . $data->to];
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

//    public function createToken(Request $request, $len = 64)
//    {
//        $token = new UserToken($request);
//        $token->save();
//        return $token;
//    }

    public function d__hasRole($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (in_array($role, $this->roles)) {
                    return true;
                }
            }
        } else {
            if (in_array($roles, $this->roles)) {
                return true;
            }
        }
        return false;
    }


}
