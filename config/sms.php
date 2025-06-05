<?php

return [
    'url' => env('SMS_API_URL', 'http://182.18.143.11/api/mt/SendSMS?'),
    'query_params' => [
        'user' => env('SMS_USER'),
        'password' => env('SMS_PASSWORD'),
        'senderid' => env('SMS_SENDER_ID', 'IEMUEM'),
        'channel' => 'trans',
        'DCS' => 0,
        'flashsms' => 0,
        'route' => '1'
    ],
    'number_field' => env('SMS_NUMBER_FIELD', 'number'),
    'message_field' => env('SMS_MESSAGE_FIELD', 'text'),
];
