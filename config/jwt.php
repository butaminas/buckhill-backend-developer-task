<?php

return [
    'key' => env('JWT_KEY', 'secret'),
    'algo' => env('JWT_ALGO', 'HS256'),
    'expiration' => env('JWT_EXPIRATION', '60'), // in minutes
];
