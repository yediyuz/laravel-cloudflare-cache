<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache;

interface CloudflareCacheInterface
{
    public function purgeEverything(): bool|string;

    /**
     * @param  array<int, string>  $prefixes
     */
    public function purgeByPrefixes(array $prefixes): bool|string;

    /**
     * @param  array<int, string>  $tags
     */
    public function purgeByTags(array $tags): bool|string;

    /**
     * @param  array<int, string>  $hosts
     */
    public function purgeByHosts(array $hosts): bool|string;

    /**
     * @param  array<int, string>  $urls
     */
    public function purgeByUrls(array $urls): bool|string;
}
