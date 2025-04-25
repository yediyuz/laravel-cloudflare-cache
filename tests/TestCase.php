<?php

declare(strict_types=1);

namespace Yediyuz\CloudflareCache\Tests;

use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as Orchestra;
use Yediyuz\CloudflareCache\CloudflareCacheServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->registerTestRoutes();
    }

    protected function getPackageProviders($app): array
    {
        return [
            CloudflareCacheServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('app.key', 'base64:' . base64_encode(Encrypter::generateKey(config()['app.cipher'])));
        config()->set('database.default', 'testing');

        config()->set('cloudflare-cache.api_email', '');
        config()->set('cloudflare-cache.api_key', '');
        config()->set('cloudflare-cache.identifier', '');
    }

    protected function registerTestRoutes(): void
    {
        $routes = static function () {
            Route::get('/home', function () {
                return 'homepage';
            })->name('home');

            Route::cache()->group(function () {
                Route::get('/content_without_tags', function () {
                    return 'content_without_tags';
                })->name('content_without_tags');
            });

            Route::cache(tags: ['tag1', 'tag2'], ttl: 600)->group(function () {
                Route::get('/content_in_args', function () {
                    return 'content_in_args';
                })->name('content_in_args');
            });

            Route::cache(['tag1', 'tag2'])->group(function () {
                Route::get('/content_in_array', function () {
                    return 'content_in_array';
                })->name('content_in_array');
            });

            Route::cache(function () {
                Route::get('/content_in_closure', function () {
                    return 'content_in_closure';
                })->name('content_in_closure');
            });
        };

        Route::middleware('web')
            ->name('cloudflare-cache.')
            ->group(function () use ($routes) {
                $routes();
            });
    }
}
