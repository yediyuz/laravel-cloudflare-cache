# Laravel Cloudflare Cache

[![Tests](https://img.shields.io/github/workflow/status/yediyuz/laravel-cloudflare-cache/tests?label=tests)](https://github.com/yediyuz/laravel-cloudflare-cache/actions/workflows/tests.yml)
[![Packagist](https://img.shields.io/packagist/v/yediyuz/laravel-cloudflare-cache.svg?style=flat-square)](https://packagist.org/packages/yediyuz/laravel-cloudflare-cache)
<!-- [![Total Downloads](https://img.shields.io/packagist/dt/yediyuz/laravel-cloudflare-cache.svg?style=flat-square)](https://packagist.org/packages/yediyuz/laravel-cloudflare-cache) -->

## Installation

You can install the package via composer:

```bash
composer require yediyuz/laravel-cloudflare-cache
```


You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-cloudflare-cache-config"
```


## Usage

```php
$cloudflareCache = new Yediyuz\CloudflareCache();
echo $cloudflareCache->echoPhrase('Hello, Yediyuz!');
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
- [Mert Aşan](https://github.com/mertasan)
- [Emre Dipi](https://github.com/emredipi)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
