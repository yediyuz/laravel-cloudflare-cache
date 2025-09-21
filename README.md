<picture>
  <source media="(prefers-color-scheme: dark)" srcset="https://banners.beyondco.de/Laravel%20Cloudflare%20Cache.png?theme=dark&packageManager=composer+require&packageName=yediyuz%2Flaravel-cloudflare-cache&pattern=architect&style=style_1&description=Serve+millions+of+requests+by+caching+with+Cloudflare&md=1&showWatermark=0&fontSize=100px&images=server">
  <source media="(prefers-color-scheme: light)" srcset="https://banners.beyondco.de/Laravel%20Cloudflare%20Cache.png?theme=light&packageManager=composer+require&packageName=yediyuz%2Flaravel-cloudflare-cache&pattern=architect&style=style_1&description=Serve+millions+of+requests+by+caching+with+Cloudflare&md=1&showWatermark=0&fontSize=100px&images=server">
  <img alt="Package Image" src="https://banners.beyondco.de/Laravel%20Cloudflare%20Cache.png?theme=light&packageManager=composer+require&packageName=yediyuz%2Flaravel-cloudflare-cache&pattern=architect&style=style_1&description=Serve+millions+of+requests+by+caching+with+Cloudflare&md=1&showWatermark=0&fontSize=100px&images=server">
</picture>

# Laravel Cloudflare Cache

<p>
    <a href="https://github.com/yediyuz/laravel-cloudflare-cache/actions"><img src="https://img.shields.io/github/actions/workflow/status/yediyuz/laravel-cloudflare-cache/tests.yml?branch=master&label=tests" alt="Test Status"></a>
    <a href="https://packagist.org/packages/yediyuz/laravel-cloudflare-cache"><img src="https://img.shields.io/packagist/v/yediyuz/laravel-cloudflare-cache.svg?style=flat-square" alt="Latest Release"></a>
    <a href="https://github.com/mertasan/tailwindcss-variables/blob/master/LICENSE"><img src="https://img.shields.io/badge/License-MIT-green.svg?label=license" alt="License"></a>
</p>

You can serve millions of requests with this package. This package provides cacheable routes for Cloudflare. Thanks to Cloudflare, your static pages are served efficiently, reducing the load on your servers if they are cached for the TTL (Time to Live) duration. You can purge the cache whenever you need with this package.

## Installation

You can install the package via composer:

```bash
composer require yediyuz/laravel-cloudflare-cache
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="cloudflare-cache-config"
```

Add environment variables to .env file

##### Using global api key:

```dotenv
CLOUDFLARE_CACHE_EMAIL=info@example.com #Cloudflare account email address
CLOUDFLARE_CACHE_KEY=XXXXXXX #Cloudflare global api key
CLOUDFLARE_CACHE_IDENTIFIER=XXXXXXX #ZONE_ID
CLOUDFLARE_DEFAULT_CACHE_TTL=600 #10 minutes
CLOUDFLARE_CACHE_DEBUG=false
```

##### Using fine-grained api token:

```dotenv
CLOUDFLARE_CACHE_API_TOKEN=XXXXXXX
CLOUDFLARE_CACHE_IDENTIFIER=XXXXXXX #ZONE_ID
CLOUDFLARE_DEFAULT_CACHE_TTL=600 #10 minutes
CLOUDFLARE_CACHE_DEBUG=false
```

### Add `Rule` on Cloudflare
To active caching on static pages, you need to add `page rule` **OR** `cache rule` on Cloudflare.

For page rule:
- If the URL matches: `www.example.com/*`
- Setting: Cache Level
- Value: Cache Everything

For the cache rule:
- Field: hostname
- Operator: equals
- Value: `example.com`
- Then: Eligible for cache

https://developers.cloudflare.com/cache/how-to/cache-rules/create-dashboard/

## Usage

### Define routes to cache
You can use cache groups for your static contents.
```php
Route::cache()->group(function () {
    Route::get('/content', function () {
        return 'content';
    });
});
```

You can use cache tags, so you can clear your caches easily. Specify custom ttl for expire time in seconds. When you do not pass ttl, it will use default ttl given in the config.
```php
Route::cache(tags: ['tag1', 'tag2'], ttl: 600)->group(function () {
    Route::get('/content_with_tags', function () {
        return 'content';
    });
});

Route::cache(tags: ['staticPages'])->group(function () {
    //
});
```

> [!WARNING]  
> Be careful caching your routes! Do not cache your dynamic pages such as admin panel or form based pages!

### Clear Cache

#### Purges everything
https://developers.cloudflare.com/cache/how-to/purge-cache/purge-everything/
```php
CloudflareCache::purgeEverything()
```

#### Purges by urls
https://developers.cloudflare.com/cache/how-to/purge-cache/purge-by-single-file/
```php
CloudflareCache::purgeByUrls([
    'https://example.com/hello',
])
```

#### Purges by prefixes (Enterprise only)
https://developers.cloudflare.com/cache/how-to/purge-cache/purge_by_prefix/
```php
CloudflareCache::purgeByPrefixes([
    'www.example.com/foo',
])
```

#### Purges by tags (Enterprise only)
https://developers.cloudflare.com/cache/how-to/purge-cache/purge-by-tags/
```php
CloudflareCache::purgeByTags([
    'staticPages',
])
```

#### Purges by hostname (Enterprise only)
https://developers.cloudflare.com/cache/how-to/purge-cache/purge-by-hostname/
```php
CloudflareCache::purgeByHosts([
    'www.example.com',
    'images.example.com',
])
```

Post update example to clear cache
```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdatePostRequest;
use App\Models\Post;
use Yediyuz\CloudflareCache\Facades\CloudflareCache;

class PostController extends Controller
{
    public function update(Post $post, UpdatePostRequest $request)
    {
        $post->update($request->validated());

        CloudflareCache::purgeByUrls([
            route('post.show', $post->id)
        ]);

        return back()->with('message', 'Post updated and url cache purged');
    }
}
```


## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](https://github.com/yediyuz/.github/blob/master/CONTRIBUTING.md) for details.

## Security

If you've found a bug regarding security please mail security@yediyuz.com instead of using the issue tracker.

## Credits
- [Emre Dipi](https://github.com/emredipi)
- [Mert Aşan](https://github.com/mertasan)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
