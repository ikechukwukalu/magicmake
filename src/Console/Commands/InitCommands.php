<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class InitCommands extends Command
{

    protected function handleAppActionPath()
    {
        $this->handlePath('Actions',
            __DIR__.'/stubs/init/app/Actions',
            'Action');
    }

    protected function handleAppContractsPath()
    {
        if (! is_dir($directory = app_path('Contracts'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Contracts'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Contracts/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Contract classes scaffolding generated successfully.');
    }

    protected function handleAppEnumsPath()
    {
        $this->handlePath('Enums',
            __DIR__.'/stubs/init/app/Enums',
            'Enum');
    }

    protected function handleAppEventsPath()
    {
        $this->handlePath('Events',
            __DIR__.'/stubs/init/app/Events',
            'Event');
    }

    protected function handleAppExceptionsPath()
    {
        $this->handlePath('Exceptions',
            __DIR__.'/stubs/init/app/Exceptions',
            'Exception');
    }

    protected function handleAppHttpPath()
    {
        $this->handlePath('Http',
            __DIR__.'/stubs/init/app/Http',
            'Http');
    }

    protected function handleAppHttpControllersPath()
    {
        $this->handlePath('Http/Controllers',
            __DIR__.'/stubs/init/app/Controllers',
            'Http/Controllers');
    }

    protected function handleAppHttpControllersAuthPath()
    {
        $this->handlePath('Http/Controllers/Auth',
            __DIR__.'/stubs/init/app/AuthControllers',
            'Http/Controllers/Auth');
    }

    protected function handleAppHttpMiddlewarePath()
    {
        $this->handlePath('Http/Middleware',
            __DIR__.'/stubs/init/app/Middleware',
            'Http/Middleware');
    }

    protected function handleAppHttpRequestsPath()
    {
        $this->handlePath('Http/Requests',
            __DIR__.'/stubs/init/app/Requests',
            'Http/Requests');
    }

    protected function handleAppHttpRequestsAuthPath()
    {
        $this->handlePath('Http/Requests/Auth',
            __DIR__.'/stubs/init/app/AuthRequests',
            'Http/Requests/Auth');
    }

    protected function handleAppListenersPath()
    {
        $this->handlePath('Listeners',
            __DIR__.'/stubs/init/app/Listeners',
            'Listener');
    }

    protected function handleAppModelsPath()
    {
        $this->handlePath('Models',
            __DIR__.'/stubs/init/app/Models',
            'Model');
    }

    protected function handleAppModelsScopesPath()
    {
        $this->handlePath('Models/Scopes',
            __DIR__.'/stubs/init/app/ModelsScopes',
            'Models/Scopes');
    }

    protected function handleAppNotificationsPath()
    {
        $this->handlePath('Notifications',
            __DIR__.'/stubs/init/app/Notifications',
            'Notification');
    }

    protected function handleAppProvidersPath()
    {
        $this->handlePath('Providers',
            __DIR__.'/stubs/init/app/Providers',
            'Provider');
    }

    protected function handleAppRepositoriesPath()
    {
        $this->handlePath('Repositories',
            __DIR__.'/stubs/init/app/Repositories',
            'Repository');
    }

    protected function handleAppRulesPath()
    {
        $this->handlePath('Rules',
            __DIR__.'/stubs/init/app/Rules',
            'Rule');
    }

    protected function handleAppServicesPath()
    {
        $this->handlePath('Services',
            __DIR__.'/stubs/init/app/Services',
            'Service');
    }

    protected function handleAppServicesAuthPath()
    {
        $this->handlePath('Services/Auth',
            __DIR__.'/stubs/init/app/AuthServices',
            'Services/Auth');
    }

    protected function handleAppTraitsPath()
    {
        $this->handlePath('Traits',
            __DIR__.'/stubs/init/app/Traits',
            'Trait');
    }

    protected function handleConfigPath()
    {
        $this->handlePath('config',
            __DIR__.'/stubs/init/config',
            'config');
    }

    protected function handleDatabasePath()
    {
        $this->handlePath('database/factories',
            __DIR__.'/stubs/init/database/factories',
            'database/factories');

        $this->handlePath('database/migrations',
            __DIR__.'/stubs/init/database/migrations',
            'database/migrations');
    }

    protected function handleLangPath()
    {
        $this->handlePath('lang/en',
            __DIR__.'/stubs/init/lang/en',
            'lang/en');
    }

    protected function handleRoutesPath()
    {
        $this->handlePath('routes/app',
            __DIR__.'/stubs/init/routes/app',
            'routes/app');

        file_put_contents(
            base_path('routes/web.php'),
            file_get_contents(__DIR__.'/stubs/init/routes/web.stub'),
            FILE_APPEND
        );

        file_put_contents(
            base_path('routes/api.php'),
            file_get_contents(__DIR__.'/stubs/init/routes/api.stub'),
            FILE_APPEND
        );

        $this->components->info('routes scaffolding generated successfully.');
    }

    protected function handleTestsPath()
    {
        $this->handlePath('tests/Feature',
            __DIR__.'/stubs/init/tests/Feature',
            'tests/Feature');
    }

    protected function handleResourcesPath()
    {
        $this->handlePath('resources/views/layouts',
            __DIR__.'/stubs/init/resources/views/layouts',
            'resources/views/layouts');

        $this->handlePath('resources/views/passwords',
            __DIR__.'/stubs/init/resources/views/passwords',
            'resources/views/passwords');

        $this->handlePath('resources/views/socialite',
            __DIR__.'/stubs/init/resources/views/socialite',
            'resources/views/socialite');

        $this->handlePath('resources/views/twofactor',
            __DIR__.'/stubs/init/resources/views/twofactor',
            'resources/views/twofactor');
    }

    private function handlePath(string $appPath, string $stubPath, string $name)
    {
        if (! is_dir($directory = app_path($appPath))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles($stubPath))
            ->each(function (SplFileInfo $file) use ($filesystem, $appPath) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path("{$appPath}/".Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info("{$name} classes scaffolding generated successfully.");
    }
}
