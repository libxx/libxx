<?php

return [
    'routes' => [
        'api' => [
            'path' => '/api',
            'middleware' => [
                \App\Module\Api\Middleware\ProcessMiddleware::class
            ],
            'routes' => [
                'ping' => [
                    'path' => '/ping',
                    'context' => '\App\Module\Api\Controller\HTTP::ping',
                ]
            ]
        ]
    ]
];