<?php

declare(strict_types=1);

test('cache bulunmayan sayfalarda session cookie mevcut olmalıdır', function () {
    $response = $this->get(route('cloudflare_cache.home'));

    $response->assertCookie('XSRF-TOKEN');
    $response->assertCookie('laravel_session');
});

test('cache bulunan sayfalarda session cookies mevcut olmamalıdır', function ($routeName) {

    $response = $this->get(route($routeName));

    $response->assertCookieMissing('XSRF-TOKEN');
    $response->assertCookieMissing('laravel_session');
})->with([
    'cloudflare_cache.content_without_tags',
    'cloudflare_cache.content_in_args',
    'cloudflare_cache.content_in_array',
    'cloudflare_cache.content_in_closure',
]);
