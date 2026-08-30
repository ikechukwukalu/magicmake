<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Ikechukwukalu\Magicmake\Generation\GenerationConflictException;
use Throwable;

class MagicInitCommand extends InitCommands
{
    protected $signature = 'magic:init
        {--force : Overwrite conflicting generated files}
        {--dry-run : Display the complete plan without writing files}';

    protected $description = 'Safely initialize the opinionated Magic Make application structure';

    public function handle()
    {
        if (! $this->laravel->environment('local')) {
            $this->components->error('This app environment is not local.');

            return self::FAILURE;
        }

        $locked = $this->laravel['config']->get('magicmake.init_lock', env('MAGIC_INIT_LOCK', true));
        if ($locked) {
            $this->components->error('This action is blocked. MAGIC_INIT_LOCK is enabled by default.');

            return self::FAILURE;
        }

        $overwrite = (bool) $this->option('force');

        try {
            $plan = $this->generationPlan();
            $this->displayPlan($plan, $overwrite);

            if ($this->option('dry-run')) {
                return $plan->conflicts($overwrite) === [] ? self::SUCCESS : self::FAILURE;
            }

            $plan->commit($overwrite);
        } catch (GenerationConflictException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->components->error('Initialization failed and file changes were rolled back: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Magic Make initialization completed safely. Optional vendor integrations are not published automatically.');

        return self::SUCCESS;
    }
}
