<?php

namespace App\Providers;

use App\Models\Customer;
use App\Services\Auth\ThrottleRequestsService;
use Illuminate\Auth\RequestGuard;
use Illuminate\Auth\SessionGuard;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Application;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //Auth::customer() for web
        SessionGuard::macro('customer', function(int|string|null $id = null): Customer|null {
            return Customer::find($id ?? Auth::user()->id);
        });

        //Auth::customer() for api
        RequestGuard::macro('customer', function(int|string|null $id = null): Customer|null {
            return Customer::find($id ?? Auth::user()->id);
        });

        $this->app->bind(ThrottleRequestsService::class, function (Application $app) {
            return new ThrottleRequestsService(
                config('api.login.maxAttempts', 3),
                config('api.login.delayMinutes', 1)
            );
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
