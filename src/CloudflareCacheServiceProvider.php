<?php

declare(strict_types=1);
namespace Yediyuz\CloudflareCache;

use Illuminate\Http\Client\Factory;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Route;
use ReflectionException;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Yediyuz\CloudflareCache\Commands\CloudflareCacheCommand;
use Yediyuz\CloudflareCache\Mixins\CloudflareRouterMixin;
use Yediyuz\CloudflareCache\Services\CloudflareService;
use Yediyuz\CloudflareCache\Services\CloudflareServiceInterface;

class CloudflareCacheServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('cloudflare_cache')
                ->hasConfigFile()
                ->hasCommand(CloudflareCacheCommand::class);
    }

    /**
     * @throws ReflectionException
     */
    public function packageRegistered(): void
    {
        $this->registerClient()
             ->registerCloudflareCache();

        // Route mixin
        Router::mixin(new CloudflareRouterMixin());
    }

    public function registerClient(): static
    {
        $this->app->bind('cloudflare_cache.client', function ($app): Factory {
            return $app[Factory::class];
        });

        $this->app->singleton(CloudflareServiceInterface::class, function ($app): CloudflareService {
            return new CloudflareService(
                $app->make('cloudflare_cache.client'),
                config('cloudflare_cache.api_email'),
                config('cloudflare_cache.api_key'),
                config('cloudflare_cache.identifier'),
            );
        });

        return $this;
    }

    public function registerCloudflareCache(): void
    {
        $this->app->bind(CloudflareCacheInterface::class, function ($app): CloudflareCache {
            return new CloudflareCache(
                $app->make(CloudflareServiceInterface::class)
            );
        });

        $this->app->alias(CloudflareCacheInterface::class, 'cloudflare_cache');
    }
}
