<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Yediyuz\CloudflareCache\Commands\CloudflareCacheCommand;

class CloudflareCacheServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package->name('laravel-cloudflare-cache')
                ->hasConfigFile()
                ->hasCommand(CloudflareCacheCommand::class);
    }
}
