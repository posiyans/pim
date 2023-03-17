<?php


namespace App\Modules\User\Classes;

use App\Models\User;
use App\Modules\Telegram\Classes\TelegramDeleteLoginCodeMessage;
use PragmaRX\Google2FA\Google2FA;

class CheckUserTwoFactorCodeClass
{
    private $user;

    private $code;

    public function __construct(User $user, $code)
    {
        $this->code = $code;
        $this->user = $user;
    }

    public function run()
    {
        $opt = $this->user->options;
        if (isset($opt['two_code']) && !empty($opt['two_code']) && $opt['two_code'] == $this->code) {
            $opt['two_code'] = '';
            $this->user->options = $opt;
            $this->deleteTelegramCode();
            $this->user->save();
            return true;
        }
        if (isset($opt['two_code']) && !empty($opt['two_code']) && in_array('google2fa', $opt['two_factor_enable'])) {
            $google2fa = new Google2FA();
            $secret = $this->user->twofa_secret;
            if ($google2fa->verifyKey($secret, $this->code)) {
                $this->deleteTelegramCode();
                return true;
            }
        }

        return throw new \Exception('Не верный код');
    }

    private function deleteTelegramCode()
    {
        (new TelegramDeleteLoginCodeMessage($this->user))->run();
    }

}
