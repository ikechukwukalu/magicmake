<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'magic:model')]
class MagicModelCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'magic:model';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'magic:model';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new magic model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->alreadyExists($name)) {
            $this->components->error("This model already exists");
            return;
        }

        $ary = explode("\\", $name);
        $model = $ary[count($ary) - 1];
        $modelVariable = lcfirst($model);
        $modelUnderScore = Str::snake($modelVariable);

        $this->callSilently("make:migration", ['name' => "create_{$modelUnderScore}s_table"]);
        $this->components->info("Migration scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:contract", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic contract scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:repository", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic repository scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:service", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic service scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:controller", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic controller scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:createRequest", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic create request scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:updateRequest", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic update request scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:deleteRequest", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic delete request scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:readRequest", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic read request scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:api", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic api route scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:test", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic test scaffolding for {$model} model generated successfully.");

        $this->callSilently("magic:factory", ['name' => "{$model}", '--variable' => $modelVariable, '--underscore' => $modelUnderScore]);
        $this->components->info("Magic factory scaffolding for {$model} model generated successfully.");

        $stub = str_replace(
            ['DummyModel', '{{ model }}'], class_basename($model), parent::buildClass($name)
        );

        $stub = str_replace(
            ['DummyModelVariable', '{{ modelVariable }}'], trim($modelVariable, '\\'), $stub
        );

        return str_replace(
            ['DummyModelUnderScore', '{{ modelUnderScore }}'], trim($modelUnderScore, '\\'), $stub
        );
    }

    /**
     * Determine if the class already exists.
     *
     * @param  string  $rawName
     * @return bool
     */
    protected function alreadyExists($rawName)
    {
        return class_exists($rawName) ||
               $this->files->exists($this->getPath($this->qualifyClass($rawName)));
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/model.stub';
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Models';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the model already exists'],
        ];
    }
}
