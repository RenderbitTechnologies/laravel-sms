<?php

namespace Renderbit\Sms\Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\Attributes\Test;
use Renderbit\Sms\Facades\Sms;
use Renderbit\Sms\SmsClient;
use Renderbit\Sms\Tests\TestCase;

class SmsIntegrationTest extends TestCase
{
    private function enableSms(): void
    {
        putenv('SMS_ENABLED=true');
        $_ENV['SMS_ENABLED'] = true;
        $_SERVER['SMS_ENABLED'] = true;
    }

    private function disableSms(): void
    {
        putenv('SMS_ENABLED=false');
        $_ENV['SMS_ENABLED'] = false;
        $_SERVER['SMS_ENABLED'] = false;
    }

    #[Test]
    public function it_sends_sms_through_the_container()
    {
        $this->enableSms();

        $mock = new MockHandler([
            new Response(200, [], 'OK'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Override the singleton binding with our mocked client
        $this->app->singleton(SmsClient::class, fn() => new SmsClient($client));

        config(['sms.url' => 'http://example.com/api']);

        /** @var SmsClient $sms */
        $sms = app(SmsClient::class);
        $result = $sms->send('1234567890', 'Integration test');

        $this->assertTrue($result);

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);

        parse_str($lastRequest->getUri()->getQuery(), $query);
        $this->assertEquals('1234567890', $query['mobile']);
        $this->assertEquals('Integration test', $query['msg']);
    }

    #[Test]
    public function it_sends_sms_through_facade_via_container()
    {
        $this->enableSms();

        $mock = new MockHandler([
            new Response(200, [], 'OK'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        $this->app->singleton(SmsClient::class, fn() => new SmsClient($client));

        config(['sms.url' => 'http://example.com/api']);

        $result = Sms::send('9876543210', 'Facade via container');

        $this->assertTrue($result);

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);

        parse_str($lastRequest->getUri()->getQuery(), $query);
        $this->assertEquals('9876543210', $query['mobile']);
        $this->assertEquals('Facade via container', $query['msg']);
    }

    #[Test]
    public function it_applies_environment_configuration()
    {
        $this->disableSms();

        $client = \Mockery::mock(Client::class);
        $client->shouldNotReceive('get');

        $this->app->singleton(SmsClient::class, fn() => new SmsClient($client));

        /** @var SmsClient $sms */
        $sms = app(SmsClient::class);
        $result = $sms->send('1234567890', 'Disabled by env');

        $this->assertTrue($result);
    }
}
