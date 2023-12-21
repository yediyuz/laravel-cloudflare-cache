<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Yediyuz\CloudflareCache\CloudflareCache
 */
class CloudflareCache extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-cloudflare-cache';
    }
}
