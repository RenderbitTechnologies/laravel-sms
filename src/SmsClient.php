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
            // Replace {{ key }} with values from $params
            foreach ($params as $key => $value) {
                $message = str_replace('{{ ' . $key . ' }}', $value, $message);
            }

            if (env('SMS_ENABLED', false)) {
                $this->client->get($this->url, [
                    'query' => array_merge(config('sms.query_params'), [
                        config('sms.number_field') => $number,
                        config('sms.message_field') => $message,
                    ])
                ]);
            } else {
                Log::info('Sms sending is disabled. You can enable it by setting the env key SMS_ENABLED with the boolean value true.');
                Log::info('Text: ' . $message . '| Phone Number: ' . $number);
            }

            Log::info("SMS sent to {$number}");
            return true;
        } catch (\Throwable $e) {
            Log::error("Failed to send SMS to {$number}: " . $e->getMessage());
            return false;
        }
    }
}
