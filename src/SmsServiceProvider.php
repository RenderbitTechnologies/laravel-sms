<?php

namespace Renderbit\Sms;

use Renderbit\Sms\SmsClient;
use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
        $this->app->singleton(SmsClient::class, fn() => new SmsClient());
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/sms.php' => config_path('sms.php'),
        ], 'config');
    }
}
