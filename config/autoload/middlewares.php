<?php

declare(strict_types=1);

use App\Middleware\AuthMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\LangMiddleware;

return [
    'http' => [
        CorsMiddleware::class,
        AuthMiddleware::class,
        LangMiddleware::class,
    ],
];
