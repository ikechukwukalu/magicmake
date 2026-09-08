<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Ikechukwukalu\Magicmake\Generation\GenerationConflictException;
use Ikechukwukalu\Magicmake\Generation\GenerationProfile;
use Illuminate\Console\Command;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Throwable;

#[AsCommand(name: 'magic:model')]
class MagicModelCommand extends Command
{
    protected $signature = 'magic:model
        {name : A single PascalCase model name}
        {--profile=standard : Generation profile: lean, repository, standard, or enterprise}
        {--path= : Project-relative feature boundary}
        {--namespace= : Explicit namespace matching an approved Composer PSR-4 mapping}
        {--force : Overwrite every conflicting feature artifact}
        {--dry-run : Display the complete feature plan without writing files}';

    protected $description = 'Create a complete Magic Make feature using preflight checks and rollback protection';

    public function handle()
    {
        $overwrite = (bool) $this->option('force');

        try {
            $plan = $this->factory()->make(
                $this->argument('name'),
                $this->option('profile') ?: GenerationProfile::STANDARD,
                $this->option('path'),
                $this->option('namespace')
            );

            $this->line('PROFILE '.strtoupper($plan->profile()));
            $this->line('TARGET '.($plan->target()->path() ?: 'Laravel defaults'));
            $this->line('NAMESPACE '.($plan->target()->namespaceName() ?: 'Laravel defaults'));
            $this->line('ARTIFACTS '.implode(', ', $plan->artifacts()));

            foreach ($plan->preview($overwrite) as $item) {
                $reason = isset($item['reason']) ? ' — '.$item['reason'] : '';
                $this->line(strtoupper($item['action']).' '.$item['path'].$reason);
            }

            if ($this->option('dry-run')) {
                return $plan->conflicts($overwrite) === [] ? self::SUCCESS : self::FAILURE;
            }

            $plan->commit($overwrite);
        } catch (InvalidArgumentException $exception) {
            $this->components->error($exception->getMessage());

            return self::INVALID;
        } catch (GenerationConflictException $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        } catch (Throwable $exception) {
            $this->components->error('Feature generation failed and file changes were rolled back: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info('Magic Make feature generated successfully.');

        return self::SUCCESS;
    }

    protected function factory()
    {
        return new FeaturePlanFactory(
            $this->laravel->basePath(),
            __DIR__.'/stubs'
        );
    }
}
