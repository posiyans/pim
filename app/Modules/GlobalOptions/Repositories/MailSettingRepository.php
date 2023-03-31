<?php

namespace App\Modules\GlobalOptions\Repositories;

use Illuminate\Support\Facades\Cache;

class MailSettingRepository
{
    private static $cacheName = 'mail_setting';

    public static function getSetting()
    {
        return Cache::remember(self::$cacheName, 3600, function () {
            $def = [
                'MAIL_HOST' => '',
                'MAIL_PORT' => 587,
                'MAIL_USERNAME' => '',
                'MAIL_PASSWORD' => '',
                'MAIL_ENCRYPTION' => 'tls',
            ];
            return (new SettingRepository())->getOptionValue('mail_setting', $def);
        });
    }

    public static function setMailConfig()
    {
        try {
            $opt = self::getSetting();
            if ($opt) {
                config(['mail.mailers.smtp.host' => $opt['MAIL_HOST']]);
                config(['mail.mailers.smtp.port' => $opt['MAIL_PORT']]);
                config(['mail.from.address' => $opt['MAIL_USERNAME']]);
                config(['mail.mailers.smtp.username' => $opt['MAIL_USERNAME']]);
                config(['mail.mailers.smtp.password' => $opt['MAIL_PASSWORD']]);
                config(['mail.mailers.smtp.encryption' => $opt['MAIL_ENCRYPTION']]);
            }
        } catch (\Exception $e) {
        }
    }

    public static function clearCache()
    {
        Cache::forget(self::$cacheName);
    }
}
