<?php


namespace App\Modules\User\Classes;

use App\Models\User;
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
            $this->user->save();
            return true;
        }
        if (isset($opt['two_code']) && !empty($opt['two_code'])) {
            $google2fa = new Google2FA();
            $secret = 'Q3URFWJTBB7MX55Y';
            if ($google2fa->verifyKey($secret, $this->code)) {
                return true;
            }
        }

        return throw new \Exception('Не верный код');
    }

}
