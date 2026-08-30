<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Throwable;

#[AsCommand(name: 'magic:api')]
class MagicApiCommand extends Command
{
    protected $name = 'magic:api';

    protected $description = 'Append a Magic Make API route block once';

    public function handle()
    {
        try {
            $plan = (new FeaturePlanFactory($this->laravel->basePath(), __DIR__.'/stubs'))
                ->routePlan($this->argument('name'));
            $plan->commit();
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::INVALID;
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Magic API route is present.');

        return self::SUCCESS;
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'A single PascalCase model name');
        $this->addOption('variable', 'var', InputOption::VALUE_REQUIRED);
        $this->addOption('underscore', 'u', InputOption::VALUE_REQUIRED);
    }
}
