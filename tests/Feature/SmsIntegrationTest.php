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
    #[Test]
    public function it_sends_sms_through_the_container()
    {
        config(['sms.enabled' => true]);

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
        config(['sms.enabled' => true]);

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
    public function it_does_not_send_when_disabled_via_config()
    {
        config(['sms.enabled' => false]);

        $client = \Mockery::mock(Client::class);
        $client->shouldNotReceive('get');

        $this->app->singleton(SmsClient::class, fn() => new SmsClient($client));

        /** @var SmsClient $sms */
        $sms = app(SmsClient::class);
        $result = $sms->send('1234567890', 'Disabled by config');

        $this->assertTrue($result);
    }
}
