# sone-modules

[![Latest Version on Packagist](https://img.shields.io/packagist/v/SequelONE/sone-modules.svg?style=flat-square)](https://packagist.org/packages/SequelONE/sone-modules)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Scrutinizer Coverage](https://img.shields.io/scrutinizer/coverage/g/SequelONE/sone-modules.svg?maxAge=86400&style=flat-square)](https://scrutinizer-ci.com/g/SequelONE/sone-modules/?branch=master)
[![Total Downloads](https://img.shields.io/packagist/dt/SequelONE/sone-modules.svg?style=flat-square)](https://packagist.org/packages/SequelONE/sone-modules)

| **Laravel** | **sone-modules** |
|-------------|---------------------|
| 5.4         | ^1.0                |
| 5.5         | ^2.0                |
| 5.6         | ^3.0                |
| 5.7         | ^4.0                |
| 5.8         | ^5.0                |
| 6.0         | ^6.0                |
| 7.0         | ^7.0                |
| 8.0         | ^8.0                |
| 9.0         | ^9.0                |
| 10.0        | ^10.0               |
| 11.0        | ^11.0               |

`sequelone/sone-modules` is a Laravel package created to manage your large Laravel app using modules. A Module is like a Laravel package, it has some views, controllers or models. This package is supported and tested in Laravel 11.

This package is a re-published, re-organised and maintained version of [pingpong/modules](https://github.com/pingpong-labs/modules), which isn't maintained anymore.

With one big bonus that the original package didn't have: **tests**.

## Install

To install via Composer, run:

``` bash
composer require sequelone/sone-modules
```

The package will automatically register a service provider and alias.

Optionally, publish the package's configuration file by running:

``` bash
php artisan vendor:publish --provider="SequelONE\sOne\Modules\sOneModulesServiceProvider"
```

### Autoloading

By default, the module classes are not loaded automatically. You can autoload your modules by adding merge-plugin to the extra section:

```json
"extra": {
    "laravel": {
        "dont-discover": []
    },
    "merge-plugin": {
        "include": [
            "Modules/*/composer.json"
        ]
    }
},
```

**Tip: don't forget to run `composer dump-autoload` afterwards.**

## Documentation

You'll find installation instructions and full documentation on [https://docs.sequel.one/](https://docs.sequel.one).

## Credits

- [SEQUEL.ONE](https://github.com/SequelONE)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
