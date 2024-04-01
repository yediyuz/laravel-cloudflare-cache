<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache;

use Illuminate\Support\Arr;
use Yediyuz\CloudflareCache\Exceptions\CloudflareCacheRequestException;
use Yediyuz\CloudflareCache\Services\CloudflareServiceInterface;

class CloudflareCache implements CloudflareCacheInterface
{
    public const TAGS_ATTR = 'cloudflare-cache-tags';

    public const TTL_ATTR = 'cloudflare-cache-ttl';

    public function __construct(private readonly CloudflareServiceInterface $service)
    {
        // .
    }

    /**
     * @return array<int, string>
     */
    public static function getIgnoredMiddlewares(): array
    {
        return Arr::where(config('cloudflare-cache.ignored_middlewares', []), static fn ($middleware) => class_exists($middleware));
    }

    /**
     * @param array<string, array<int, string>|true> $options
     */
    protected function purge(array $options = []): bool|string
    {
        $purgeRequest = $this->service->post('/purge_cache', $options);

        $responseData = $purgeRequest->json();

        if (! $purgeRequest->successful()) {
            throw CloudflareCacheRequestException::requestError($purgeRequest->status(), $responseData['errors'][0]['message'] ?? '-', $responseData['errors'][0]['code'] ?? null);
        }

        if (! ($responseData['success'] ?? false)) {
            return false;
        }

        return $responseData['result']['id'];
    }

    public function purgeEverything(): bool|string
    {
        return $this->purge([
            'purge_everything' => true,
        ]);
    }

    /**
     * @param array<int, string> $prefixes
     */
    public function purgeByPrefixes(array $prefixes): bool|string
    {
        return $this->purge([
            'prefixes' => $prefixes,
        ]);
    }

    /**
     * @param array<int, string> $tags
     */
    public function purgeByTags(array $tags): bool|string
    {
        return $this->purge([
            'tags' => $tags,
        ]);
    }

    /**
     * @param array<int, string> $hosts
     */
    public function purgeByHosts(array $hosts): bool|string
    {
        return $this->purge([
            'hosts' => $hosts,
        ]);
    }

    /**
     * @param array<int, string> $urls
     */
    public function purgeByUrls(array $urls): bool|string
    {
        return $this->purge([
            'files' => $urls,
        ]);
    }

    public function isActive(): bool
    {
        if (app()->runningUnitTests()) {
            return true;
        }

        if (! config('cloudflare-cache.identifier')) {
            return false;
        }

        if (! config('cloudflare-cache.api_token')) {
            if (! config('cloudflare-cache.api_email') || ! config('cloudflare-cache.api_key')) {
                return false;
            }
        }

        if (config('cloudflare-cache.debug')) {
            return true;
        }

        return app()->isProduction();
    }
}
