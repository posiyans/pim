<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

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
        'twofa_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'options' => 'array',
        'aliases' => 'array',
        'roles' => 'array',
        'moderator' => 'boolean',
        'hide' => 'boolean',
        'two_factor' => 'boolean',
        'twofa_secret' => 'encrypted',
    ];


    /**
     * Получить log модели
     */
    public function log()
    {
        return $this->morphMany('App\Modules\Log\Models\Log', 'commentable');
    }

    public function getModeratorAttribute()
    {
        return in_array('moderator', $this->roles);
    }


    public function setModeratorAttribute($val)
    {
        if ($val) {
            if (!$this->moderator) {
                $roles = $this->roles;
                $roles[] = 'moderator';
                $this->roles = array_values($roles);
            }
        } else {
            if ($this->moderator) {
                $roles = $this->roles;
                unset($roles[array_search('moderator', $roles)]);
                $this->roles = array_values($roles);
            }
        }
    }

    public function getAdminAttribute()
    {
        return in_array('admin', $this->roles);
    }

    public function setAdminAttribute($val)
    {
        if ($val) {
            if (!$this->admin) {
                $roles = $this->roles;
                $roles[] = 'admin';
                $this->roles = array_values($roles);
            }
        } else {
            if ($this->admin) {
                $roles = $this->roles;
                unset($roles[array_search('admin', $roles)]);
                $this->roles = array_values($roles);
            }
        }
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
