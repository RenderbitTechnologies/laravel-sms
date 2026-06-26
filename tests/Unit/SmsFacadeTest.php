<?php

namespace Renderbit\Sms\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Renderbit\Sms\Facades\Sms;
use Renderbit\Sms\SmsClient;
use Renderbit\Sms\Tests\TestCase;

class SmsFacadeTest extends TestCase
{
    #[Test]
    public function it_resolves_smsclient_through_facade()
    {
        $instance = Sms::getFacadeRoot();
        $this->assertInstanceOf(SmsClient::class, $instance);
    }

    #[Test]
    public function it_forwards_calls_to_smsclient()
    {
        config(['sms.url' => 'http://example.com/api']);
        config(['sms.enabled' => false]);

        // Should log instead of sending when disabled
        $result = Sms::send('1234567890', 'Facade test');

        $this->assertTrue($result);
    }
}
