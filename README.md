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

Magic Make supports only the following CI-proven Laravel/PHP combinations:

| Laravel | PHP | Support tier |
| --- | --- | --- |
| 11 | 8.2–8.4 | Compatibility only; security support ended March 12, 2026 |
| 12 | 8.2–8.5 | Security fixes only through February 24, 2027 |
| 13 | 8.3–8.5 | Supported; security fixes through Q1 2028 |

Every listed Laravel/PHP combination is exercised in development CI. Laravel 11 compatibility is retained for established applications, but current Composer policy blocks its known vulnerable releases by default. The legacy jobs consciously bypass resolution blocking only to execute compatibility tests and still report every advisory; Laravel 12–13 jobs retain strict blocking and audit failure. Laravel 12 receives security fixes but its general bug-fix window ended August 13, 2026. PHP 7.3 and Laravel 8–10 were previously declared without matching syntax or CI evidence and are no longer advertised. The `php: ^8.2` dependency floor does not advertise unlisted future PHP releases; support is limited to the combinations in this table until their complete CI jobs pass.

Magic Make installs only the dependencies needed by the scaffolder itself. Generated authentication, notification, activity-log, permissions, browser-detection, and other optional integrations require their corresponding suggested Composer packages. Run `composer suggests ikechukwukalu/magicmake` and install the integrations selected for your application profile.

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

Initialization resolves and displays every destination before writing. An unchanged rerun is a no-op, customized files are reported as conflicts, and route includes are appended only once. Use `--dry-run` to inspect the plan. Use `--force` only after reviewing the displayed conflicts; it explicitly replaces conflicting generated files. Optional vendor integrations are not published automatically.

### Response modes

Initialization creates `config/magicmake.php` with the application-wide response mode. Supported modes are `view`, `json`, and `auto`; the default is `auto`.

```dotenv
MAGIC_RESPONSE_MODE=auto
```

Response mode resolution is deterministic: a method-level override takes precedence over a controller-level override, which takes precedence over the application-wide default. Request content negotiation is consulted only when the resolved mode is `auto`.

Generated CRUD controllers explicitly use JSON mode to preserve the established API behavior:

```php
use Ikechukwukalu\Magicmake\Response\ResponseMode;

protected string|null $responseMode = ResponseMode::JSON;
```

Set the controller property to `ResponseMode::VIEW`, `ResponseMode::AUTO`, or `null` to select a controller-wide mode or inherit the application default. The generated base-controller methods also accept a final method-level response-mode argument. View mode requires a component name; selecting it without one raises an explicit error instead of silently returning JSON.

Application services remain presentation-agnostic and return `ResponseData`. Controllers and response helpers decide whether that data becomes a view or JSON response.

## MODEL BASED CLASSES

To generate all model based prepared classes.

``` shell
php artisan magic:model UserKyc
```

Feature names must be single PascalCase PHP identifiers. The command preflights the model, migration, contract, repository, service, controller, requests, factory, test, and API route as one plan. A conflict prevents every write, an unchanged rerun is non-destructive, and a failed write rolls the feature back. `--dry-run` displays the plan and `--force` explicitly overwrites conflicting artifacts.

For an established project, updating the package and running only `magic:model` does not replace files previously created by `magic:init`. Existing helpers, the application base controller, and `ResponseData` remain unchanged, so legacy JSON behavior is preserved. Adopting the newer initialization scaffolding is a separate, explicit migration; see [UPGRADE.md](UPGRADE.md).

### Generation profiles

v5.0.1 added the Repository profile and shared repository autobinding. v5.1.0 makes that registration safe for both modern Laravel applications and upgraded applications that retain the legacy bootstrap structure.

`--profile=standard` remains the default and preserves the established Magic Make feature structure.

- `lean`: model, migration, factory, and focused model test.
- `repository`: Lean plus a repository contract, repository implementation, and an array/ID-based service returning `ResponseData` without HTTP request dependencies.
- `standard`: model, migration, contract, repository, service, controller, create/update/delete/read requests, API route, factory, and feature test.
- `enterprise`: Standard plus a dedicated service provider with repository binding. For modular targets, the provider also loads the feature route and migrations.

Standard and Repository safely maintain one application-level `App\Providers\RepositoryServiceProvider`. In a modern Laravel 11–13 application, Magic Make creates or updates `bootstrap/providers.php`; in a conservatively recognized upgraded application that retains Laravel's legacy bootstrap structure, it preserves or safely updates the canonical `config/app.php` `ServiceProvider::defaultProviders()->merge([...])->toArray()` list instead. It never creates an unused modern registry for a legacy application. Existing unrelated provider content and registrations are preserved. An exact existing `$this->app` or zero-argument `app()` binding is retained whether it uses `bind`, `singleton`, or `scoped` lifecycle semantics (including their `*If` variants), or `instance` with a directly constructed repository; the application-selected lifecycle remains authoritative. Duplicate, dynamic, unparseable, or otherwise ambiguous registration structures and conflicting or ambiguous implementations stop the complete plan before any write; `--force` cannot bypass those semantic conflicts. Calls on unrelated receivers do not satisfy an application-container binding.

```shell
php artisan magic:model Invoice --profile=lean
php artisan magic:model Invoice --profile=repository
php artisan magic:model Invoice --profile=standard
php artisan magic:model Invoice --profile=enterprise
```

### Modular targets and namespaces

Use a project-relative `--path` to keep every selected artifact inside a feature boundary. Magic Make derives the namespace from the most specific Composer PSR-4 mapping and displays the resolved profile, target, namespace, artifacts, and file actions before writing.

```shell
php artisan magic:model Invoice \
    --profile=enterprise \
    --path=app/Domains/Billing
```

With the normal `"App\\": "app/"` mapping, the resolved namespace is `App\Domains\Billing`. An explicit namespace may be provided with `--namespace`; it must match the selected path. A namespace without a path derives its path only when the Composer mapping is unambiguous.

```shell
php artisan magic:model Invoice \
    --profile=enterprise \
    --path=app/Domains/Billing \
    --namespace='App\Domains\Billing'
```

Modular Standard output includes a boundary-local route file but does not register that route automatically. Its repository binding is added to the shared application provider. Repository-profile output has no route. Enterprise providers retain their boundary-specific bindings and load their boundary-local route and migration directories; Enterprise does not change the shared provider or `bootstrap/providers.php`.

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

When using the individual `magic:contract` and `magic:repository` commands instead of a Standard or Repository profile, add the binding to `app/Providers/RepositoryServiceProvider.php` manually.

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
