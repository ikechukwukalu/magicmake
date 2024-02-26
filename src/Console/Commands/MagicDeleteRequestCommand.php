<?php

namespace Ikechukwukalu\Magicmake\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'magic:deleteRequest')]
class MagicDeleteRequestCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'magic:deleteRequest';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'magic:deleteRequest';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new magic request class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DeleteRequest';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        if ($this->alreadyExists(str_replace('.php', '', $this->getPath($name)))) {
            $this->components->error("This DeleteRequest already exists");
            return;
        }

        $model = $name;
        $modelVariable = $this->option('variable');
        $modelUnderScore = $this->option('underscore');
        $name = "{$name}DeleteRequest";

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
        return __DIR__.'/stubs/deleteRequest.stub';
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
        return 'App\Http\Requests';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', 'f', InputOption::VALUE_NONE, 'Create the class even if the request already exists'],
            ['variable', 'var', InputOption::VALUE_REQUIRED, 'Create a model variable value for this request class'],
            ['underscore', 'u', InputOption::VALUE_REQUIRED, 'Create a model underscore value for this request class'],
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
        $name = "{$name}DeleteRequest";
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'.str_replace('\\', '/', $name).'.php';
    }
}
