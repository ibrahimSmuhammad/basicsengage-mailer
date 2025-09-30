<?php

namespace Ibrahim\BasicsEngageMail;

use Illuminate\Support\ServiceProvider;
use Illuminate\Mail\MailManager;
use Ibrahim\BasicsEngageMail\Mail\Transports\BasicsEngageTransport;

class BasicsEngageServiceProvider extends ServiceProvider
{
    public function register()
    {

    }
    public function boot()
    {
        $this->app->resolving(MailManager::class, function (MailManager $manager) {
            $manager->extend('basicsengage', function ($config) {
                return new \Ibrahim\BasicsEngageMail\Mail\Transports\BasicsEngageTransport($config);
            });
        });
    }

}