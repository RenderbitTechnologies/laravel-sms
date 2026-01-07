<?php

namespace Renderbit\Sms\Tests\Unit;

use GuzzleHttp\Client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Log;
use Mockery;
use Renderbit\Sms\SmsClient;
use Renderbit\Sms\Tests\TestCase;

class SmsClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /** @test */
    public function it_sends_sms_successfully()
    {
        // Mock Guzzle Client
        $mock = new MockHandler([
            new Response(200, [], 'OK'),
        ]);
        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // Enable SMS
        config(['sms.url' => 'http://example.com/api']);
        // We need to use putenv because the code uses env() directly
        putenv('SMS_ENABLED=true');

        $sms = new SmsClient($client);
        $result = $sms->send('1234567890', 'Hello {{ name }}', ['name' => 'John']);

        $this->assertTrue($result);

        // Check if request was made
        $this->assertCount(0, $mock); // 0 remaining, meaning 1 consumed

        $lastRequest = $mock->getLastRequest();
        $this->assertNotNull($lastRequest);
        $this->assertEquals('GET', $lastRequest->getMethod());

        parse_str($lastRequest->getUri()->getQuery(), $query);
        $this->assertEquals('1234567890', $query['mobile']); // Configured in TestCase
        $this->assertEquals('Hello John', $query['msg']);
    }

    /** @test */
    public function it_logs_when_sms_is_disabled()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('Sms sending is disabled. You can enable it by setting the env key SMS_ENABLED with the boolean value true.');

        Log::shouldReceive('info')
            ->once()
            ->with('Text: Hello John| Phone Number: 1234567890');

        Log::shouldReceive('info')
            ->once()
            ->with('SMS sent to 1234567890');

        // Disable SMS
        // Note: putenv might not be enough if $_ENV is populated
        putenv('SMS_ENABLED=false');
        $_ENV['SMS_ENABLED'] = false;
        $_SERVER['SMS_ENABLED'] = false;

        // Mock Client but it shouldn't be called
        $client = Mockery::mock(Client::class);
        $client->shouldNotReceive('get');

        $sms = new SmsClient($client);
        $result = $sms->send('1234567890', 'Hello {{ name }}', ['name' => 'John']);

        $this->assertTrue($result);

        // Reset env
        putenv('SMS_ENABLED=true');
        $_ENV['SMS_ENABLED'] = true;
        $_SERVER['SMS_ENABLED'] = true;
    }

    /** @test */
    public function it_handles_exceptions_gracefully()
    {
        putenv('SMS_ENABLED=true');

        $mock = new MockHandler([
            new Response(500, [], 'Error'), // Or actually throw exception
        ]);
        // To force an exception from Guzzle, we can simulate network error or 400/500 if configured to throw
        // By default Guzzle throws on 4xx/5xx

        $handlerStack = HandlerStack::create($mock);
        $client = new Client(['handler' => $handlerStack]);

        // But here we want to test catch block.
        // Let's mock throwing an exception
        $client = Mockery::mock(Client::class);
        $client->shouldReceive('get')->andThrow(new \Exception('Network Error'));

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Failed to send SMS to 1234567890: Network Error');
            });

        $sms = new SmsClient($client);
        $result = $sms->send('1234567890', 'Message');

        $this->assertFalse($result);
    }
}
