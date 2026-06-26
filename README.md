# Laravel SMS

[![Tests](https://github.com/RenderbitTechnologies/laravel-sms/actions/workflows/tests.yml/badge.svg)](https://github.com/RenderbitTechnologies/laravel-sms/actions/workflows/tests.yml)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/renderbit/laravel-sms.svg?style=flat-square)](https://packagist.org/packages/renderbit/laravel-sms)
[![PHP Version](https://img.shields.io/packagist/php-v/renderbit/laravel-sms.svg?style=flat-square)](https://packagist.org/packages/renderbit/laravel-sms)
[![Laravel Version](https://img.shields.io/badge/Laravel-10%20%7C%2011%20%7C%2012-blue?style=flat-square)](https://laravel.com)
[![License](https://img.shields.io/packagist/l/renderbit/laravel-sms.svg?style=flat-square)](https://packagist.org/packages/renderbit/laravel-sms)

A Laravel package to send transactional SMS messages through supported SMS gateways. Built with simplicity and robustness in mind.

## 🚀 Features

* Simple API to send SMS via Facade or dependency injection
* API-based architecture — works with any SMS gateway
* Template variable substitution (`{{ name }}` placeholders)
* Configurable query parameters, number field, and message field
* Disable SMS in non-production environments via config
* Laravel-native configuration, logging, and service provider
* PHP 8.1+ with Guzzle HTTP client

## 📦 Installation

```bash
composer require renderbit/laravel-sms
```

Laravel auto-discovers the service provider. No manual registration needed.

## 🛠 Configuration

Publish the configuration file:

```bash
php artisan vendor:publish --provider="Renderbit\Sms\SmsServiceProvider" --tag=config
```

This will publish `config/sms.php`. Example contents:

```php
return [
    'enabled' => env('SMS_ENABLED', false),
    'url' => env('SMS_API_URL', 'http://182.18.143.11/api/mt/SendSMS?'),
    'query_params' => [
        'user' => env('SMS_USER'),
        'password' => env('SMS_PASSWORD'),
        'senderid' => env('SMS_SENDER_ID', 'IEMUEM'),
        'channel' => 'trans',
        'DCS' => 0,
        'flashsms' => 0,
        'route' => '1',
    ],
    'number_field' => env('SMS_NUMBER_FIELD', 'number'),
    'message_field' => env('SMS_MESSAGE_FIELD', 'text'),
];
```

Update your `.env` file:

```env
SMS_ENABLED=true
SMS_USER=
SMS_PASSWORD=
SMS_SENDER_ID='IEMUEM'
SMS_API_URL='http://182.18.143.11/api/mt/SendSMS?'
SMS_NUMBER_FIELD='number'
SMS_MESSAGE_FIELD='text'
```

> **Note:** SMS sending is disabled by default. Set `SMS_ENABLED=true` in your `.env` or `sms.enabled` in config to enable it.

## ✉️ Usage

Send an SMS using the Facade or `SmsClient`:

### Using Facade

```php
use Sms;

Sms::send('+919999999999', 'Hello, your OTP is 123456');
```

### Using Dependency Injection

```php
use Renderbit\Sms\SmsClient;

class NotificationService
{
    public function __construct(protected SmsClient $sms) {}

    public function notify(string $phone, string $message): bool
    {
        return $this->sms->send($phone, $message);
    }
}
```

### Template Variables

You can pass replacement values in a third parameter:

```php
Sms::send('+919999999999', 'Hello {{ name }}, your code is {{ code }}', [
    'name' => 'John',
    'code' => 'ABC123',
]);
// Sends: "Hello John, your code is ABC123"
```

## ✅ Return Value

The `send` method returns `true` on success (or when SMS is disabled) and `false` on failure:

```php
if (! Sms::send($phone, $message)) {
    // Log failure or retry
}
```

Errors are logged automatically via Laravel's logger.

## 🧪 Testing

Run the package's test suite:

```bash
vendor/bin/phpunit
```

The suite covers unit tests (SmsClient, SmsServiceProvider, Facade) and feature tests (integration through the Laravel container), **22 tests** with **51 assertions**.

To control SMS behavior in your own tests:

```php
// Disable SMS in tests (sending is logged, not actually sent)
config(['sms.enabled' => false]);
```

## 📁 Project Structure

```
config/
  sms.php              — Default configuration published to the app
src/
  SmsClient.php         — Core SMS sending logic with template substitution
  SmsServiceProvider.php — Laravel service provider (singleton binding, config publish)
  Facades/
    Sms.php              — Facade accessor for SmsClient
tests/
  Unit/
    SmsClientTest.php         — 13 tests covering send(), edge cases, and config
    SmsServiceProviderTest.php — Tests for singleton binding, boot, and config publishing
    SmsFacadeTest.php          — Tests for facade resolution and call forwarding
  Feature/
    SmsIntegrationTest.php    — 3 tests for end-to-end flows through the container
```

## 🤝 Contributing

Pull requests are welcome! For major changes, please open an issue first to discuss what you'd like to change.

## 📄 License

This package is open-sourced software licensed under the [MIT license](LICENSE).
