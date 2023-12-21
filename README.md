# :package_title

[![Tests](https://img.shields.io/github/workflow/status/:vendor_slug/:package_slug/tests?label=tests)](https://github.com/:vendor_slug/:package_slug/actions/workflows/tests.yml)<!--deleteCoverage--><!--deleteCodecov-->
[![codecov](https://codecov.io/gh/:vendor_slug/:package_slug/branch/:default_branch/graph/badge.svg?token=:codecov_token)](https://codecov.io/gh/:vendor_slug/:package_slug)<!--/deleteCodecov--><!--/deleteCoverage-->
[![Packagist](https://img.shields.io/packagist/v/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug)
<!-- [![Total Downloads](https://img.shields.io/packagist/dt/:vendor_slug/:package_slug.svg?style=flat-square)](https://packagist.org/packages/:vendor_slug/:package_slug) -->

## Installation

You can install the package via composer:

```bash
composer require :vendor_slug/:package_slug
```

<!--hasDatabase-->
You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag=":package_slug-migrations"
php artisan migrate
```
<!--/hasDatabase-->

<!--hasConfig-->
You can publish the config file with:

```bash
php artisan vendor:publish --tag=":package_slug-config"
```
<!--/hasConfig-->

<!--hasViews-->
Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag=":package_slug-views"
```
<!--/hasViews-->

## Usage

```php
$variable = new VendorName\Skeleton();
echo $variable->echoPhrase('Hello, VendorName!');
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

## Credits<!--delete-->
- [Spatie](https://github.com/spatie)<!--/delete-->
- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
