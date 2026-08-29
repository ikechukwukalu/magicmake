<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Ikechukwukalu\Magicmake\Generation\GenerationPlan;
use Ikechukwukalu\Magicmake\Generation\InitPlanFactory;
use Illuminate\Console\Command;

abstract class InitCommands extends Command
{
    protected function generationPlan()
    {
        $factory = new InitPlanFactory(
            $this->laravel->basePath(),
            __DIR__.'/stubs/init'
        );

        return $factory->make();
    }

    protected function displayPlan(GenerationPlan $plan, $overwrite)
    {
        foreach ($plan->preview($overwrite) as $item) {
            $this->line(strtoupper($item['action']).' '.$item['path']);
        }
    }
}
