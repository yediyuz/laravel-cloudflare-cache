<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yediyuz\CloudflareCache\Facades\CloudflareCache as CloudflareCacheFacade;

class CloudflarePagesMiddleware
{
    public function handle(Request $request, Closure $next, string $ttl, string $tags): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if (! $this->shouldCacheResponse($request, $response)) {
            return $response;
        }

        if (! $ttl) {
            $ttl = $request->attributes->get(
                CloudflareCache::TTL_ATTR,
                config('cloudflare-cache.default_cache_ttl') ?? 600
            );
        }

        $response->headers->set('Cache-Control', "max-age=$ttl, public");
        $request->attributes->set(CloudflareCache::TTL_ATTR, $ttl);
        $response->headers->remove('set-cookie');

        if ($tags = $this->getCacheTags($request, $tags)) {
            $response->headers->set('Cache-Tags', implode(',', $tags));
        }

        return $response;
    }

    /**
     * @return array<int, string>
     */
    protected function getCacheTags(Request $request, string $tags): array
    {
        /**
         * cache('tag1')
         * cache(['tag1', 'tag2']).
         */
        $tags = $tags ? explode(';', $tags) : [];
        $tags = array_unique(array_merge($request->attributes->get(CloudflareCache::TAGS_ATTR, []), $tags));
        $request->attributes->set(CloudflareCache::TAGS_ATTR, $tags);

        return $tags;
    }

    protected function getCacheTTL(Request $request): int
    {
        if ($request->attributes->has(CloudflareCache::TTL_ATTR)) {
            return $request->attributes->get(CloudflareCache::TTL_ATTR);
        }

        return config('cloudflare-cache.cache_ttl') ?? 600;
    }

    public function shouldCacheResponse(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if (! $response->isSuccessful()) {
            return false;
        }

        if (! CloudflareCacheFacade::isActive() && ! app()->runningUnitTests()) {
            return false;
        }

        if (auth()->check()) {
            return false;
        }

        return true;
    }
}
