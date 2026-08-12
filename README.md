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

This release requires:

- PHP 8.2 or newer.
- Laravel 12.

Optional integrations referenced by generated application scaffolding must be installed separately when used.

## SOURCE AND RELEASE POLICY

Development, tests, feature branches, and pull requests are maintained in the private `ikechukwukalu/magic-make` development source repository. This public repository is the release destination for reviewed package versions.

Documentation in this repository distinguishes published behavior from approved development work. Generation safety, profiles, modular target paths and namespaces, and explicit response modes remain unreleased until a separately authorized package synchronization and release.

## UNRELEASED DEVELOPMENT CONTRACT

The development source has an approved Phase 2 contract for future package synchronization. These options are **not available in the published v3.0.0 package**.

- `lean`: model, migration, factory, and focused model test.
- `standard`: the existing opinionated model, migration, contract, repository, service, controller, four requests, API route, factory, and feature-test structure. Standard remains the default.
- `enterprise`: Standard plus a feature service provider, repository binding, and modular route/migration registration.

The approved command shape is:

```shell
php artisan magic:model Invoice \
    --profile=enterprise \
    --path=app/Domains/Billing \
    --namespace='App\Domains\Billing'
```

Target paths are project-relative. When only `--path` is supplied, the namespace is derived from Composer PSR-4 mappings. When only `--namespace` is supplied, the path is derived only if the mapping is unambiguous. Mismatched, unsafe, or unmapped path/namespace combinations fail during preflight. Selected modular artifacts—including routes, providers, factories, migrations, and tests—remain inside the target boundary.

Modular Standard routes require explicit host-application loading. Enterprise generates a provider that binds the repository and loads boundary-local routes and migrations; the host application must explicitly register that provider. The generator does not silently mutate application provider configuration.

## STEPS TO INSTALL

``` shell
composer require ikechukwukalu/magicmake
```

## SET UP LARAVEL

``` shell
php artisan install:api
```

## INIT CLASSES

To initialize prepared classes for a new laravel app. This would only run when `env('APP_ENV') === local` and `env(MAGIC_INIT_LOCK) === false`.

``` shell
php artisan magic:init
```

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

Add to `bootstrap/providers.php`.

```php
/*
    * Package Service Providers...
    */
App\Providers\EventServiceProvider::class,
App\Providers\MacroServiceProvider::class,
App\Providers\RepositoryServiceProvider::class,
```

Add to `bootstrap/app.php`

```php
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'check.email.verification' => \App\Http\Middleware\CheckEmailVerification::class,
            'check.user.is.admin' => \App\Http\Middleware\CheckIfUserIsAdmin::class,
            'check.phone.verification' => \App\Http\Middleware\CheckPhoneVerification::class,
        ]);
    })
```

Add to `vite.config.js`

```js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/js/app.js',
                'resources/css/app.css',
            ],
            refresh: true,
        }),
    ],
});
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

## NOTE

### Publish notification blade

Add the following line above this code line `@if ($emailData->action)` in the `notification.blade.php` file:

```html
@if (isset($emailData->highlightText))
@component('mail::panel', ['style' => 'background-color: #f0f8ff; border-radius: 0.5rem; padding: 16px;text-align: center'])
<div style="text-align: center;">
{{ $emailData->highlightText }}
</div>
@endcomponent
@endif

```

### App notification helpers

```php
$userNotificationData = new UserNotificationData($user->id, $title, $text);
$user->notify(new DatabaseNotification($userNotificationData->toObject()));

$emailData = new EmailData(subject: $title, lines: [$text], from: env('MAIL_FROM_ADDRESS'),
    remark: null, action: false, action_text: null,
    action_url: null, attachements: null);
$user->notify(new EmailNotification($emailData->toObject()));

$smsData = new SmsData($user->name, $text);
$user->notify(new SmsNotification($smsData->toObject()));
```

### Advanced search and table filter helpers

Sample usage:

```php
public function getPaginated(int $pageSize): LengthAwarePaginator
{
    /**
     * Search a parent table via the user_id foreign key on the user_deliveries table
     */
    $search = advancedSearch('user', 'user_id', ['unit_number', 'name', 'email', 'first_name', 'last_name', 'middle_name']);

    /**
     * Search a child table via the user_delivery_id foreign key on the user_delivery_items table
     */
    $search2 = advancedSearch('userDeliveryItems', 'user_delivery_id', ['tracking_number', 'name', 'delivery_vendor'], 'user_delivery_items');

    return UserDelivery::with(['user', 'userDeliveryItems'])
                ->search($search)
                ->search($search2)
                ->orWhere(function($query) {
                    $query->search('ref_number');
                })
                ->order()
                ->date()
                ->filter($this->whiteList)
                ->paginate(pageSize($pageSize));
}
```

## LICENSE

The MM package is a software licensed under the [Apache 2.0 license](https://www.apache.org/licenses/LICENSE-2.0).
