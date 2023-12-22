<?php

/** @noinspection PhpUndefinedClassInspection */

declare(strict_types=1);

return [
    'ignored_middlewares' => [
        \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
        \Illuminate\Cookie\Middleware\EncryptCookies::class,
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\EncryptCookies::class,
    ],

    'api_email' => env('CLOUDFLARE_EMAIL'),

    'api_key' => env('CLOUDFLARE_KEY'),

    'identifier' => env('CLOUDFLARE_IDENTIFIER'),
];
