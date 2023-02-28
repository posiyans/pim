<?php

namespace App\Modules\Sms\Classes;


use App\Models\User;

class SendSms
{
    private $user;
    private $text;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function text($text)
    {
        $this->tex = $text;
        return $this;
    }

    public function code($length = 3)
    {
        $min = pow(10, ($length)) + 1;
        $max = pow(10, ($length + 1)) - 1;
        $this->text = mt_rand($min, $max);
        return $this;
    }

    public function run()
    {
        $apikey = env('SMS_API_KEY');
        $smsru = new Sms($apikey);
        $data = new \stdClass();
        $data->to = $smsru->parserPhone($this->user->phone);
        $data->text = $this->text;
        $data->partner_id = env('SMS_PARTNER');
        if (env('SMS_TEST')) {
            $data->test = 1;
        }
        $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную
        if ($sms->status == "OK") {
            return $data->text;
        } else {
            throw new \Exception('Ошибка отправки СМС');
            return false;
        }
    }
}