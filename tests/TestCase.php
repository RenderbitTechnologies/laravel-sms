<?php

namespace Renderbit\Sms\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Renderbit\Sms\SmsServiceProvider;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app)
    {
        return [
            SmsServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default config
        $app['config']->set('sms.url', 'http://example.com/api/send');
        $app['config']->set('sms.query_params', [
            'user' => 'test_user',
            'password' => 'test_password',
            'senderid' => 'TESTID',
        ]);
        $app['config']->set('sms.number_field', 'mobile');
        $app['config']->set('sms.message_field', 'msg');
    }
}
