<?php

return [
    'auth0' => [
        'client_id' => env('AUTH0_CLIENT_ID'),
        'client_secret' => env('AUTH0_CLIENT_SECRET'),
        'redirect' => env('AUTH0_REDIRECT_URI'),
        'base_url' => env('AUTH0_BASE_URL'),
    ],
];