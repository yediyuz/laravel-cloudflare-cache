<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Yediyuz\CloudflareCache\Facades\CloudflareCache as CloudflareCacheFacade;

class CloudflarePagesMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        if ($this->shouldCacheResponse($request, $response)) {
            $ttl = $this->getCacheTTL($request);
            $response->headers->add(['Cache-Control' => "max-age=$ttl, public"]);
            $response->headers->remove('set-cookie');

            if ($this->hasCacheTags($request)) {
                $tags = implode(',', $this->getCacheTags($request));
                $response->headers->add(['Cache-Tags' => $tags]);
            }
        }

        return $response;
    }

    protected function hasCacheTags(Request $request): bool
    {
        return filled($request->attributes->get(CloudflareCache::TAGS_ATTR));
    }

    /**
     * @return array<int, string>
     */
    protected function getCacheTags(Request $request): array
    {
        return array_merge(array_unique($request->attributes->get(CloudflareCache::TAGS_ATTR, [])));
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
