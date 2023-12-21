<?php

declare(strict_types=1);

use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;


test('example test', function () {
    Http::fake();

    $service = app()->make(\Yediyuz\CloudflareCache\Services\CloudflareServiceInterface::class);
    $service->get('purge_cache');

    Http::assertSentCount(1);
    Http::assertSent(function (Request $request, Response $response) {
        $this->assertTrue($request->hasHeader('X-Auth-Email'));
        $this->assertTrue($request->hasHeader('X-Auth-Key'));
        $this->assertSame($request->url(), 'https://api.cloudflare.com/client/v4/zones//purge_cache');

        expect($response)
            ->status()
            ->toBe(200);

        return $request;
    });
});


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
