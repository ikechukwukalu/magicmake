<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'magic:repository')]
class MagicRepositoryCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'magic:repository';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'magic:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new magic repository class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Repository';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->alreadyExists(str_replace('.php', '', $this->getPath($name)))) {
            $this->components->error("This Repository already exists");
            return;
        }

        $ary = explode("\\", $name);
        $model = $ary[count($ary) - 1];
        $modelVariable = $this->option('variable');
        $modelUnderScore = $this->option('underscore');
        $name = "{$name}Repository";

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
        return __DIR__.'/stubs/repository.stub';
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
        return $rootNamespace.'\Repositories';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the repository already exists'],
            ['variable', 'var', InputOption::VALUE_REQUIRED, 'Create a model variable value for this repository class'],
            ['underscore', 'u', InputOption::VALUE_REQUIRED, 'Create a model underscore value for this repository class'],
        ];
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = "{$name}Repository";
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }
}
