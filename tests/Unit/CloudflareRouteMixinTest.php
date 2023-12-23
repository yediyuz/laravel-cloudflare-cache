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
    [['foo', new stdClass()], ['foo']],
    [['foo', '', ' ', '0', '1'], ['foo', '0', '1']],
]);

test('cache tag türleri beklendiği gibi filtrelenmelidir', function ($tags, $expectedTags, $ttl) {

    $request = request();

    $request->attributes->remove(CloudflareCache::TAGS_ATTR);
    $request->attributes->remove(CloudflareCache::TTL_ATTR);

    $this->assertFalse($request->attributes->has(CloudflareCache::TAGS_ATTR));
    $this->assertFalse($request->attributes->has(CloudflareCache::TTL_ATTR));

    Route::cache($tags, $ttl);

    ray($request->attributes);

    expect($request->attributes)
        ->has(CloudflareCache::TAGS_ATTR)
        ->toBeTrue()
        ->get(CloudflareCache::TAGS_ATTR)
        ->toEqual($expectedTags)
        ->and($request->attributes)
        ->match($ttl, [
            null => fn ($attributes) => $attributes->has(CloudflareCache::TTL_ATTR)->toBeFalse(),
            600  => fn ($attributes) => $attributes->has(CloudflareCache::TTL_ATTR)->toBeTrue(),
        ]);

})->with('cache_mixin_tag_types')
  ->with([
      null,
      600,
  ]);
