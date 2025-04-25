<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Yediyuz\CloudflareCache\CloudflareCache;

dataset('cache_mixin_tag_types', [
    ['foo', ['foo']],
    [['foo', 'bar'], ['foo', 'bar']],
    [['foo', '123'], ['foo', '123']],
    [['foo', []], ['foo']],
    [['foo', ['bar']], ['foo']],
    [['foo', true, false], ['foo']],
    [['foo', new stdClass], ['foo']],
    [['foo', '', ' ', '0', '1'], ['foo', '0', '1']],
]);

test('cache tag türleri beklendiği gibi filtrelenmelidir', function ($tags, $expectedTags, $ttl) {

    $request = request();

    $request->attributes->remove(CloudflareCache::TAGS_ATTR);
    $request->attributes->remove(CloudflareCache::TTL_ATTR);

    $this->assertFalse($request->attributes->has(CloudflareCache::TAGS_ATTR));
    $this->assertFalse($request->attributes->has(CloudflareCache::TTL_ATTR));

    Route::cache($tags, $ttl)->get('/test', function () {
        return 'test';
    });

    $response = $this->get('test');
    expect($response)->assertHeader('Cache-Tags', implode(',', $expectedTags));

})->with('cache_mixin_tag_types')
    ->with([
        null,
        600,
    ]);

test('cache tag must exist in the header only if used', function () {
    $response = $this->get('content_without_tags');
    $this->assertFalse($response->headers->has('Cache-Tags'));

    $response = $this->get('content_in_args');
    $this->assertTrue($response->headers->has('Cache-Tags'));
});
