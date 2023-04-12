<?php

return [
    'key' => env('JWT_KEY', 'secret'),
    'algo' => env('JWT_ALGO', 'HS256'),
];
