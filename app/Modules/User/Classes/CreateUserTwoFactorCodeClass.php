<?php


namespace App\Modules\User\Classes;

use App\Models\User;
use App\Modules\Telegram\Classes\TelegramDeleteLoginCodeMessage;

class CreateUserTwoFactorCodeClass
{
    private $user;


    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function run()
    {
        $code = rand(100000, 999999);
        $opt = $this->user->options;
        $opt['two_code'] = $code;
        $this->user->options = $opt;
        $this->user->save();
        (new TelegramDeleteLoginCodeMessage($this->user))->run();
        return $code;
    }
}
