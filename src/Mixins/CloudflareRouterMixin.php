<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache\Mixins;

use Closure;
use Illuminate\Support\Arr;
use Yediyuz\CloudflareCache\CloudflareCache;
use Yediyuz\CloudflareCache\CloudflarePagesMiddleware;

/**
 * @mixin \Illuminate\Routing\Router
 */
class CloudflareRouterMixin
{
    public function cache(): Closure
    {
        return function (string|array|Closure $tags = null, int $ttl = null) {

            $router = app()->make('router');

            if (! in_array(CloudflarePagesMiddleware::class, $router->middlewarePriority, true)) {
                $router->middlewarePriority = Arr::prepend($router->middlewarePriority, CloudflarePagesMiddleware::class);
            }

            $routeRegistrar = function () {
                return $this->withoutMiddleware(CloudflareCache::getIgnoredMiddlewares())->middleware(CloudflarePagesMiddleware::class);
            };

            $registerTtl = static function ($ttl, $request = null) {
                if ($ttl === null) {
                    return;
                }

                $request = $request ?? request();
                $request->attributes->set(CloudflareCache::TTL_ATTR, $ttl);
            };

            // cache(function() { ... }
            if ($tags instanceof Closure) {
                $registerTtl($ttl);

                return $routeRegistrar()->group($tags);
            }

            /**
             * cache('tag1')
             * cache(['tag1', 'tag2']).
             */
            $tags = Arr::where(Arr::wrap($tags), static fn ($tag) => is_string($tag) && filled($tag));

            $request = request();
            if ($currentTags = $request->attributes->get(CloudflareCache::TAGS_ATTR)) {
                $request->attributes->set(CloudflareCache::TAGS_ATTR, array_merge($currentTags, $tags));
            }
            $registerTtl($ttl, $request);

            return $routeRegistrar();
        };
    }
}
