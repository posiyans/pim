<?php

namespace App\Modules\Sms\Classes;


use App\Models\User;

class SendSms
{
    private $sms;

    public function __construct()
    {
        $apikey = env('SMS_API_KEY');
        $this->sms = new Sms($apikey);
    }

    public function getBalance()
    {
        $res = $this->sms->getBalance();
        if ($res->status_code == 100) {
            return $res->balance;
        }
        return '';
    }

    public function text(User $user, $text)
    {
        $data = new \stdClass();
        $data->to = $this->sms->parserPhone($user->phone);
        $data->text = $text;
        $data->partner_id = 2316;
        if (env('SMS_TEST')) {
            $data->test = 1;
        }
        $sms = $this->sms->send_one($data); // Отправка сообщения и возврат данных в переменную
        if ($sms->status == "OK") {
            return $data->text;
        } else {
            throw new \Exception('Ошибка отправки СМС');
            return false;
        }
    }

    public function code(User $user, $length = 3)
    {
        $min = pow(10, ($length)) + 1;
        $max = pow(10, ($length + 1)) - 1;
        $this->text = mt_rand($min, $max);
        $data = new \stdClass();
        $data->to = $this->sms->parserPhone($user->phone);
        $data->text = $this->text;
        $data->partner_id = 2316;
        if (env('SMS_TEST')) {
            $data->test = 1;
        }
        $sms = $this->sms->send_one($data); // Отправка сообщения и возврат данных в переменную
        if ($sms->status == "OK") {
            return $data->text;
        } else {
            throw new \Exception('Ошибка отправки СМС');
            return false;
        }
    }

}