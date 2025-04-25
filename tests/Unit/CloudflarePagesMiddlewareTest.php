<?php

declare(strict_types=1);

it('should have session cookie on non-cached pages', function () {
    $response = $this->get(route('cloudflare-cache.home'));

    $response->assertCookie('XSRF-TOKEN');
    $response->assertCookie('laravel_session');
});

it('should not have session cookies on cached pages', function ($routeName) {

    $response = $this->get(route($routeName));

    $response->assertCookieMissing('XSRF-TOKEN');
    $response->assertCookieMissing('laravel_session');
})->with([
    'cloudflare-cache.content_without_tags',
    'cloudflare-cache.content_in_args',
    'cloudflare-cache.content_in_array',
    'cloudflare-cache.content_in_closure',
]);
