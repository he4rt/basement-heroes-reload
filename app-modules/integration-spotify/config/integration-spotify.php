<?php

declare(strict_types=1);

return [
    'scopes' => explode(',', (string) env('SPOTIFY_SCOPES', 'user-read-currently-playing')),

    'polling' => [
        'enabled' => env('SPOTIFY_POLLING_ENABLED', true),
        'interval' => env('SPOTIFY_POLLING_INTERVAL', 10),
    ],
];
