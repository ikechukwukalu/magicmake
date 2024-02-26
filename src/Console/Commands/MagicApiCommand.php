<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'magic:api')]
class MagicApiCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'magic:api';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'magic:api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new magic api class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Api';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $model = $name;
        $modelVariable = $this->option('variable');
        $modelUnderScore = $this->option('underscore');
        $name = "{$name}Api";

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
        return __DIR__.'/stubs/api.stub';
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
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the api already exists'],
            ['variable', 'var', InputOption::VALUE_REQUIRED, 'Create a model variable value for this api class'],
            ['underscore', 'u', InputOption::VALUE_REQUIRED, 'Create a model underscore value for this api class'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return bool|null
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function handle()
    {
        $name = $this->qualifyClass($this->getNameInput());
        $stub = $this->buildClass($name);

        file_put_contents(
            base_path('routes/api.php'),
            $stub,
            FILE_APPEND
        );
    }
}
