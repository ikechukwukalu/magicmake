<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;

class MagicInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'magic:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold the numerous classes';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (env('APP_ENV', 'production') != 'local') {
            return;
        }

        if (env('MAGIC_INIT_LOCK', true) === true) {
            return;
        }

        $this->handleAppActionPath();
        $this->handleAppContractsPath();
        $this->handleAppEnumsPath();
        $this->handleAppEventsPath();
        $this->handleAppExceptionsPath();
        $this->handleAppHttpPath();
        $this->handleAppHttpControllersPath();
        $this->handleAppHttpControllersAuthPath();
        $this->handleAppHttpMiddlewarePath();
        $this->handleAppHttpRequestsPath();
        $this->handleAppHttpRequestsAuthPath();
        $this->handleAppListenersPath();
        $this->handleAppModelsPath();
        $this->handleAppModelsScopesPath();
        $this->handleAppNotificationsPath();
        $this->handleAppProvidersPath();
        $this->handleAppRepositoriesPath();
        $this->handleAppRulesPath();
        $this->handleAppServicesPath();
        $this->handleAppServicesAuthPath();
        $this->handleAppTraitsPath();

        $this->handleConfigPath();

        $this->handleDatabasePath();

        $this->handleLangPath();

        $this->handleRoutesPath();

        $this->handleTestsPath();

        $this->handleResourcesPath();

        $this->callSilently("vendor:publish", ["--provider" => "Laragear\TwoFactor\TwoFactorServiceProvider"]);
        $this->components->info("Laragear TwoFactor migration and config file published");

        $this->callSilently("vendor:publish", ["--provider" => "Spatie\Activitylog\ActivitylogServiceProvider", "--tag" => "activitylog-migrations"]);
        $this->components->info("Spatie ActivityLog migration and config file published");

        $this->callSilently("vendor:publish", ["--provider" => "Spatie\Permission\PermissionServiceProvider"]);
        $this->components->info("Spatie Permissions migration and config file published");
    }

    private function handleAppActionPath()
    {
        if (! is_dir($directory = app_path('Actions'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Actions'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Actions/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Action classes scaffolding generated successfully.');
    }

    private function handleAppContractsPath()
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

    private function handleAppEnumsPath()
    {
        if (! is_dir($directory = app_path('Enums'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Enums'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Enums/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Enum classes scaffolding generated successfully.');
    }

    private function handleAppEventsPath()
    {
        if (! is_dir($directory = app_path('Events'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Events'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Events/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Event classes scaffolding generated successfully.');
    }

    private function handleAppExceptionsPath()
    {
        if (! is_dir($directory = app_path('Exceptions'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Exceptions'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Exceptions/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Exception classes scaffolding generated successfully.');
    }

    private function handleAppHttpPath()
    {
        if (! is_dir($directory = app_path('Http'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Http'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Http classes scaffolding generated successfully.');
    }

    private function handleAppHttpControllersPath()
    {
        if (! is_dir($directory = app_path('Http/Controllers'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Controllers'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Http/Controllers classes scaffolding generated successfully.');
    }

    private function handleAppHttpControllersAuthPath()
    {
        if (! is_dir($directory = app_path('Http/Controllers/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/AuthControllers'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Controllers/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Http/Controllers/Auth classes scaffolding generated successfully.');
    }

    private function handleAppHttpMiddlewarePath()
    {
        if (! is_dir($directory = app_path('Http/Middleware'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Middleware'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Middleware/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Http/Middleware classes scaffolding generated successfully.');
    }

    private function handleAppHttpRequestsPath()
    {
        if (! is_dir($directory = app_path('Http/Requests'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Requests'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Requests/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Http/Requests classes scaffolding generated successfully.');
    }

    private function handleAppHttpRequestsAuthPath()
    {
        if (! is_dir($directory = app_path('Http/Requests/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/AuthRequests'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Http/Requests/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Http/Requests/Auth classes scaffolding generated successfully.');
    }

    private function handleAppListenersPath()
    {
        if (! is_dir($directory = app_path('Listeners'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Listeners'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Listeners/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Listener classes scaffolding generated successfully.');
    }

    private function handleAppModelsPath()
    {
        if (! is_dir($directory = app_path('Models'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Models'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Models/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Models classes scaffolding generated successfully.');
    }

    private function handleAppModelsScopesPath()
    {
        if (! is_dir($directory = app_path('Models/Scopes'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/ModelsScopes'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Models/Scopes/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Models/Scopes classes scaffolding generated successfully.');
    }

    private function handleAppNotificationsPath()
    {
        if (! is_dir($directory = app_path('Notifications'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Notifications'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Notifications/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Notification classes scaffolding generated successfully.');
    }

    private function handleAppProvidersPath()
    {
        if (! is_dir($directory = app_path('Providers'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Providers'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Providers/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Provider classes scaffolding generated successfully.');
    }

    private function handleAppRepositoriesPath()
    {
        if (! is_dir($directory = app_path('Repositories'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Repositories'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Repositories/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Repository classes scaffolding generated successfully.');
    }

    private function handleAppRulesPath()
    {
        if (! is_dir($directory = app_path('Rules'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Rules'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Rules/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Rule classes scaffolding generated successfully.');
    }

    private function handleAppServicesPath()
    {
        if (! is_dir($directory = app_path('Services'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Services'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Services/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Service classes scaffolding generated successfully.');
    }

    private function handleAppServicesAuthPath()
    {
        if (! is_dir($directory = app_path('Services/Auth'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/AuthServices'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Services/Auth/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Service/Auth classes scaffolding generated successfully.');
    }

    private function handleAppTraitsPath()
    {
        if (! is_dir($directory = app_path('Traits'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/app/Traits'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    app_path('Traits/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('Trait classes scaffolding generated successfully.');
    }

    private function handleConfigPath()
    {
        if (! is_dir($directory = base_path('config'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/config'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('config/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('config files scaffolding generated successfully.');
    }

    private function handleDatabasePath()
    {
        if (! is_dir($directory = base_path('database/factories'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = base_path('database/migrations'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/database/factories'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('database/factories/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('database/factories files scaffolding generated successfully.');

        collect($filesystem->allFiles(__DIR__.'/stubs/init/database/migrations'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('database/migrations/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('database/migrations files scaffolding generated successfully.');
    }

    private function handleLangPath()
    {
        if (! is_dir($directory = base_path('lang/en'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/lang/en'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('lang/en/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('lang/en files scaffolding generated successfully.');
    }

    private function handleRoutesPath()
    {

        if (! is_dir($directory = base_path('routes/app'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/routes/app'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('routes/app/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('routes/app files scaffolding generated successfully.');

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

    private function handleTestsPath()
    {
        if (! is_dir($directory = base_path('tests/Feature'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/tests/Feature'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('tests/Feature/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('tests/Feature files scaffolding generated successfully.');
    }

    private function handleResourcesPath()
    {
        if (! is_dir($directory = base_path('resources/views/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = base_path('resources/views/passwords'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = base_path('resources/views/socialite'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = base_path('resources/views/twofactor'))) {
            mkdir($directory, 0755, true);
        }

        $filesystem = new Filesystem;

        collect($filesystem->allFiles(__DIR__.'/stubs/init/resources/views/layouts'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('resources/views/layouts/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('resources/views/layouts files scaffolding generated successfully.');

        collect($filesystem->allFiles(__DIR__.'/stubs/init/resources/views/passwords'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('resources/views/passwords/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('resources/views/passwords files scaffolding generated successfully.');

        collect($filesystem->allFiles(__DIR__.'/stubs/init/resources/views/socialite'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('resources/views/socialite/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('resources/views/socialite files scaffolding generated successfully.');

        collect($filesystem->allFiles(__DIR__.'/stubs/init/resources/views/twofactor'))
            ->each(function (SplFileInfo $file) use ($filesystem) {
                $filesystem->copy(
                    $file->getPathname(),
                    base_path('resources/views/twofactor/'.Str::replaceLast('.stub', '.php', $file->getFilename()))
                );
            });

        $this->components->info('resources/views/twofactor files scaffolding generated successfully.');
    }
}
