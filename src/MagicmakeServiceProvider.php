<?php

namespace Ikechukwukalu\Magicmake;

use Illuminate\Support\ServiceProvider;
use Ikechukwukalu\Magicmake\Console\Commands\MagicApiCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicContractCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicControllerCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicCreateRequestCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicDeleteRequestCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicFactoryCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicInitCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicModelCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicReadRequestCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicRepositoryCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicServiceCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicTestCommand;
use Ikechukwukalu\Magicmake\Console\Commands\MagicUpdateRequestCommand;

class MagicmakeServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MagicApiCommand::class,
                MagicControllerCommand::class,
                MagicContractCommand::class,
                MagicCreateRequestCommand::class,
                MagicDeleteRequestCommand::class,
                MagicFactoryCommand::class,
                MagicInitCommand::class,
                MagicModelCommand::class,
                MagicReadRequestCommand::class,
                MagicRepositoryCommand::class,
                MagicServiceCommand::class,
                MagicTestCommand::class,
                MagicUpdateRequestCommand::class
            ]);
        }
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
