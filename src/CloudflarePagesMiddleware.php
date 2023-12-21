<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CloudflarePagesMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);
        if ($this->shouldCacheResponse($request, $response)) {
            $response->headers->add(['Cache-Control' => 'max-age=600, public']);
            $response->headers->remove('set-cookie');
        }

        return $response;
    }

    public function shouldCacheResponse(Request $request, Response $response): bool
    {
        if (! $request->isMethod('GET')) {
            return false;
        }

        if (! $response->isSuccessful()) {
            return false;
        }

        if (! app()->isProduction() && ! app()->runningUnitTests()) {
            return false;
        }

        if (auth()->check()) {
            return false;
        }

        return true;
    }
}
