<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache\Mixins;

use Closure;
use Illuminate\Routing\RouteRegistrar;
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
        return function ($tags = null) {

            $router = app()->make('router');

            if (! in_array(CloudflarePagesMiddleware::class, $router->middlewarePriority, true)) {
                $router->middlewarePriority = Arr::prepend($router->middlewarePriority, CloudflarePagesMiddleware::class);
            }

            $routeRegistrar = function () {
                return $this->withoutMiddleware(CloudflareCache::getIgnoredMiddlewares())->middleware(CloudflarePagesMiddleware::class);
            };

            // cache()->group(fun...
            if (is_null($tags)) {
                return $routeRegistrar();
            }

            // cache(function() { ... }
            if ($tags instanceof Closure) {
                return $routeRegistrar()->group($tags);
            }

            // cache('tag1', 'tag2')
            if (! is_array($tags)) {
                $tags = func_get_args();
            }

            // cache(['tag1', 'tag2'])
            foreach ($tags as $index => $value) {
                $tags[$index] = (string) $value;
            }

            $request = request();
            $currentTags = $request->attributes->get(CloudflareCache::TAGS_ATTR, []);
            $request->attributes->set(CloudflareCache::TAGS_ATTR, array_merge($currentTags, $tags));

            return $routeRegistrar();
        };
    }
}
