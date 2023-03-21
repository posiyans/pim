<?php

namespace App\Providers;

use App\Modules\GlobalOptions\Repositories\SettingRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            $opt = (new SettingRepository())->getOptionValue('mail_setting', false);
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
}
