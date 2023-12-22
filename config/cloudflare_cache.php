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

    // The codes written by Emre Dipi will be committed by him.
];
