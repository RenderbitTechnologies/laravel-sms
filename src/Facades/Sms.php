<?php

namespace Renderbit\Sms\Facades;

use Renderbit\Sms\SmsClient;
use Illuminate\Support\Facades\Facade;

class Sms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return SmsClient::class;
    }
}
