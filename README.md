# MAGIC MAKE

[![Latest Version on Packagist](https://img.shields.io/packagist/v/ikechukwukalu/magicmake?style=flat-square)](https://packagist.org/packages/ikechukwukalu/magicmake)
[![Quality Score](https://img.shields.io/scrutinizer/quality/g/ikechukwukalu/magicmake/main?style=flat-square)](https://scrutinizer-ci.com/g/ikechukwukalu/magicmake/)
[![Code Quality](https://img.shields.io/codefactor/grade/github/ikechukwukalu/magicmake?style=flat-square)](https://www.codefactor.io/repository/github/ikechukwukalu/magicmake)
<!-- [![Known Vulnerabilities](https://snyk.io/test/github/ikechukwukalu/magicmake/badge.svg?style=flat-square)](https://security.snyk.io/package/composer/ikechukwukalu%2Fmagicmake) -->
[![Github Workflow Status](https://img.shields.io/github/actions/workflow/status/ikechukwukalu/magicmake/magicmake.yml?branch=main&style=flat-square)](https://github.com/ikechukwukalu/magicmake/actions/workflows/magicmake.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/ikechukwukalu/magicmake?style=flat-square)](https://packagist.org/packages/ikechukwukalu/magicmake)
[![Licence](https://img.shields.io/packagist/l/ikechukwukalu/magicmake?style=flat-square)](https://github.com/ikechukwukalu/magicmake/blob/main/LICENSE.md)

A Laravel scaffolding package for an opinionated Laravel coding style.

## REQUIREMENTS

- PHP 7.3+
- Laravel 8+

## STEPS TO INSTALL

``` shell
composer require ikechukwukalu/magicmake
```

## INIT CLASSES

To initialize prepared classes for a new laravel app.

``` shell
php artisan magic:init
```

This would only run when `env('APP_ENV') === local` and `env(MAGIC_INIT_LOCK) === false`.

## MODEL BASED CLASSES

To generate all model based prepared classes.

``` shell
php artisan magic:model UserKyc
```

To generate individual model based prepared classes.

``` shell
php artisan magic:contract UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:repository UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:service UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:controller UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:createRequest UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:updateRequest UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:deleteRequest UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:readRequest UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:api UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:test UserKyc --variable=userKyc --underscore=user_kyc

php artisan magic:factory UserKyc --variable=userKyc --underscore=user_kyc
```

### Note

Add this to `config/api.php`.

```php
    'paginate' => [
        ...
        'user_kyc' => [
            'pageSize' => 10,
        ],
    ],
```

Add this to `app/Providers/RepositoryServiceProvider.php`.

```php
use App\Contracts\UserKycRepositoryInterface;
use App\Repositories\UserKycRepository;


public function register(): void
{
    ...
    $this->app->bind(UserKycRepositoryInterface::class, UserKycRepository::class);
}
```


## FINISHING SETUP

If you did run `php artisan magic:init`.

Add to the `composer.json` file.

```json
"autoload": {
    "psr-4": {
        "App\\": "app/",
        "Database\\Factories\\": "database/factories/",
        "Database\\Seeders\\": "database/seeders/"
    },
    "files": [
        "app/Http/Helpers.php"
    ]
},
```

After that run:

```shell
composer dump-autoload
```

Add to the `config/app.php`.

```php
/*
    * Package Service Providers...
    */
App\Providers\MacroServiceProvider::class,
App\Providers\RepositoryServiceProvider::class,
```

Add to `app/Http/Kernel.php` in `protected $middlewareAliases`

```php
protected $middlewareAliases = [
    ....
    'check.email.verification' => \App\Http\Middleware\CheckEmailVerification::class,
]
```

### Project setup

```shell
php artisan ui bootstrap
npm install
composer install
php artisan migrate --seed
```

### Run development server

```shell
npm run build
php artisan serve
php artisan test
```

## LICENSE

The MM package is a software licensed under the [Apache 2.0 license](https://www.apache.org/licenses/LICENSE-2.0).
