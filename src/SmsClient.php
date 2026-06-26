<?php

namespace Renderbit\Sms;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class SmsClient
{
    protected Client $client;
    protected string $url;

    public function __construct(Client $client = null)
    {
        $this->client = $client ?? new Client();
        $this->url = config('sms.url');
    }

    public function send(string $number, string $message, array $params = []): bool
    {
        try {
            if ($number === '' || $message === '') {
                Log::warning('SMS not sent: number or message is empty.');
                return false;
            }

            // Replace {{ key }} with values from $params,
            // skipping non-stringable values to avoid runtime warnings.
            foreach ($params as $key => $value) {
                $replacement = is_scalar($value) || (is_object($value) && method_exists($value, '__toString'))
                    ? (string) $value
                    : null;
                if ($replacement !== null) {
                    $message = str_replace('{{ ' . $key . ' }}', $replacement, $message);
                }
            }

            if (config('sms.enabled')) {
                $this->client->get($this->url, [
                    'query' => array_merge(config('sms.query_params'), [
                        config('sms.number_field') => $number,
                        config('sms.message_field') => $message,
                    ])
                ]);

                Log::info("SMS sent to {$number}");
            } else {
                Log::info('Sms sending is disabled. You can enable it by setting the config key sms.enabled to true.');
                Log::info('Text: ' . $message . '| Phone Number: ' . $number);
            }

            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send SMS to {$number}: " . $e->getMessage());
            return false;
        }
    }
}
