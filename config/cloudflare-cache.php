<?php

declare(strict_types=1);

return [
    'debug' => env('CLOUDFLARE_CACHE_DEBUG', false),

    'api_email' => env('CLOUDFLARE_CACHE_EMAIL'),

    'api_key' => env('CLOUDFLARE_CACHE_KEY'),

    'identifier' => env('CLOUDFLARE_CACHE_IDENTIFIER'),

    'default_cache_ttl' => env('CLOUDFLARE_DEFAULT_CACHE_TTL'),

    'ignored_middlewares' => [
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\EncryptCookies::class,
    ],
];
