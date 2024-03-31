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

            $parameters = [
                'ttl'  => $ttl,
                'tags' => $tags instanceof Closure
                    ? ''
                    : collect(Arr::wrap($tags))
                        ->where(static fn ($tag) => is_string($tag) && filled($tag))
                        ->implode(';'),
            ];

            $routeRegistrar = function () use ($parameters) {
                return $this->withoutMiddleware(CloudflareCache::getIgnoredMiddlewares())->middleware(CloudflarePagesMiddleware::class . ":{$parameters['ttl']},{$parameters['tags']}");
            };

            // cache(function() { ... }
            if ($tags instanceof Closure) {
                return $routeRegistrar()->group($tags);
            }

            return $routeRegistrar();
        };
    }
}
