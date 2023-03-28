<?php

namespace App\Providers;

use App\Modules\GlobalOptions\Repositories\MailSettingRepository;
use Illuminate\Mail\MailServiceProvider as OldMailServiceProvider;

class MailServiceProvider extends OldMailServiceProvider
{
    /**
     * Register the Illuminate mailer instance.
     *
     * @return void
     */
    protected function registerIlluminateMailer()
    {
        MailSettingRepository::setMailConfig();
        parent::registerIlluminateMailer();
    }
}
