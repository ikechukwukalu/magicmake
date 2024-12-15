<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

class MagicInitCommand extends InitCommands
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
            $this->components->error("This app environment is not local");
            return;
        }

        if (env('MAGIC_INIT_LOCK', true) === true) {
            $this->components->error("This action is blocked. MAGIC_INIT_LOCK is enabled by default");
            return;
        }

        $this->handleAppActionPath();
        $this->handleAppContractsPath();
        $this->handleAppEnumsPath();
        $this->handleAppEventsPath();
        $this->handleAppExceptionsPath();
        $this->handleAppFacadesPath();
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
}
