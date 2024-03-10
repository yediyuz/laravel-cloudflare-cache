<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Yediyuz\CloudflareCache\CloudflareCache
 *
 * @method static bool        isActive()
 * @method static bool|string purgeEverything()
 * @method static bool|string purgeByPrefixes(array $prefixes)
 * @method static bool|string purgeByTags(array $tags)
 * @method static bool|string purgeByHosts(array $hosts)
 * @method static bool|string purgeByUrls(array $urls)
 */
class CloudflareCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'cloudflare-cache';
    }
}
