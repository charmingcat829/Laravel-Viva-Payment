<?php

return [
    'viva' => [
        'api_key' => env('VIVA_API_KEY'),
        'merchant_id' => env('VIVA_MERCHANT_ID'),
        'public_key' => env('VIVA_PUBLIC_KEY'),
        'environment' => env('VIVA_ENVIRONMENT', 'production'),
        'client_id' => env('VIVA_CLIENT_ID'),
        'client_secret' => env('VIVA_CLIENT_SECRET'),
    ],
];
