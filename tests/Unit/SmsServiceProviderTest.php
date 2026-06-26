<?php

namespace Renderbit\Sms\Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use Renderbit\Sms\SmsClient;
use Renderbit\Sms\SmsServiceProvider;
use Renderbit\Sms\Tests\TestCase;

class SmsServiceProviderTest extends TestCase
{
    #[Test]
    public function it_registers_smsclient_as_a_singleton()
    {
        $instance1 = $this->app->make(SmsClient::class);
        $instance2 = $this->app->make(SmsClient::class);

        $this->assertSame($instance1, $instance2);
    }

    #[Test]
    public function it_binds_smsclient_to_the_container()
    {
        $this->assertTrue($this->app->bound(SmsClient::class));
    }

    #[Test]
    public function it_registers_the_service_provider()
    {
        $providers = $this->app->getLoadedProviders();
        $this->assertArrayHasKey(SmsServiceProvider::class, $providers);
        $this->assertTrue($providers[SmsServiceProvider::class]);
    }

    #[Test]
    public function it_publishes_config()
    {
        $provider = new SmsServiceProvider($this->app);
        $provider->boot();

        $publishes = SmsServiceProvider::pathsToPublish(SmsServiceProvider::class);

        $this->assertCount(1, $publishes);

        // The provider registers __DIR__.'/../config/sms.php' which resolves
        // to src/../config/sms.php (a literal key with '..' in it)
        $configPath = __DIR__ . '/../../config/sms.php';
        $expectedPath = config_path('sms.php');

        // Check the key matches by realpath since array key may have '..'
        $keys = array_keys($publishes);
        $this->assertEquals(
            realpath($configPath),
            realpath($keys[0])
        );
        $this->assertEquals($expectedPath, $publishes[$keys[0]]);
    }
}
