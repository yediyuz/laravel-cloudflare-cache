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
