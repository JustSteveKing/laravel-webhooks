<?php

declare(strict_types=1);

return [
    'signing' => [
        'header' => env('WEBHOOK_HEADER', 'Signature'),
        'key' => env('WEBHOOK_SIGNING_KEY'),
    ],

    'user_agent' => [
        'name' => env('WEBHOOK_USER_AGENT', 'Laravel_Webhooks')
    ],

    'request' => [
        'timeout' => env('WEBHOOK_TIMEOUT', 15),
    ]
];
