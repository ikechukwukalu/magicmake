<?php

namespace Ikechukwukalu\Magicmake\Generation;

use Illuminate\Support\Str;
use InvalidArgumentException;

class FeaturePlanFactory
{
    /** @var string */
    private $basePath;

    /** @var string */
    private $stubPath;

    /** @var Psr4NamespaceResolver */
    private $resolver;

    public function __construct($basePath, $stubPath, ?Psr4NamespaceResolver $resolver = null)
    {
        $this->basePath = rtrim(str_replace('\\', '/', $basePath), '/');
        $this->stubPath = rtrim($stubPath, '/');
        $this->resolver = $resolver ?: new Psr4NamespaceResolver($this->basePath);
    }

    public function make($name, $profile = GenerationProfile::STANDARD, $targetPath = null, $namespace = null)
    {
        $this->validateName($name);
        $profile = GenerationProfile::normalize($profile);
        $target = $this->resolver->resolve($targetPath, $namespace);
        $artifacts = GenerationProfile::artifacts($profile);
        $plan = new FeatureGenerationPlan($this->basePath, $profile, $target, $artifacts);
        $context = $this->context($name, $target);

        foreach ($artifacts as $artifact) {
            $this->addArtifact($plan, $artifact, $context, $target, $profile);
        }

        if (in_array($profile, [GenerationProfile::STANDARD, GenerationProfile::REPOSITORY], true)) {
            $this->addSharedRepositoryBinding($plan, $context);
        }

        return $plan;
    }

    public function routePlan($name)
    {
        $this->validateName($name);
        $context = $this->context($name, new GenerationTarget(null, null));
        $plan = new GenerationPlan($this->basePath);

        return $plan->addUniqueAppend(
            $this->basePath.'/routes/api.php',
            $this->render('api.stub', $context)
        );
    }

    private function addArtifact(FeatureGenerationPlan $plan, $artifact, array $context, GenerationTarget $target, $profile)
    {
        if ($artifact === 'migration') {
            $plan->addFile($this->migrationPath($context['table'], $target), $this->render('migration.stub', $context));
            return;
        }

        if ($artifact === 'api') {
            $route = $this->render('api.stub', $context);
            if ($target->isModular()) {
                $plan->addFile($this->targetRoot($target).'/Routes/api.php', "<?php\n\nuse Illuminate\\Support\\Facades\\Route;\n".$route);
            } else {
                $plan->addUniqueAppend($this->basePath.'/routes/api.php', $route);
            }
            return;
        }

        if ($artifact === 'provider') {
            $providerPath = $target->isModular()
                ? $this->targetRoot($target).'/Providers/'.$context['model'].'ServiceProvider.php'
                : $this->basePath.'/app/Providers/'.$context['model'].'ServiceProvider.php';
            $plan->addFile($providerPath, $this->providerContent($context, $target));
            return;
        }

        $definitions = $this->artifactDefinitions($context, $target);
        if (! isset($definitions[$artifact])) {
            throw new InvalidArgumentException("Artifact [{$artifact}] is not supported by profile [{$profile}].");
        }

        $definition = $definitions[$artifact];
        $stub = $artifact === 'service' && $profile === GenerationProfile::REPOSITORY
            ? 'repositoryService.stub'
            : $definition['stub'];
        $content = $this->render($stub, $context);
        $content = $this->localizeNamespaces($content, $context);
        $content = $this->addModularBaseImports($content, $artifact, $context);
        if ($artifact === 'model' && $target->isModular()) {
            $factory = '\\'.$context['factoryNamespace'].'\\'.$context['model'].'Factory';
            $method = "\n    protected static function newFactory()\n    {\n        return {$factory}::new();\n    }\n";
            $content = preg_replace('/\n}\s*$/', $method."\n}\n", $content);
        }
        $this->validateNamespace($content, $definition['namespace'], $artifact);
        $plan->addFile($definition['path'], $content);
    }

    private function artifactDefinitions(array $context, GenerationTarget $target)
    {
        $name = $context['model'];

        if (! $target->isModular()) {
            return [
                'model' => $this->definition('model.stub', "app/Models/{$name}.php", 'App\\Models'),
                'contract' => $this->definition('contract.stub', "app/Contracts/{$name}RepositoryInterface.php", 'App\\Contracts'),
                'repository' => $this->definition('repository.stub', "app/Repositories/{$name}Repository.php", 'App\\Repositories'),
                'service' => $this->definition('service.stub', "app/Services/{$name}Service.php", 'App\\Services'),
                'controller' => $this->definition('controller.stub', "app/Http/Controllers/{$name}Controller.php", 'App\\Http\\Controllers'),
                'createRequest' => $this->definition('createRequest.stub', "app/Http/Requests/{$name}CreateRequest.php", 'App\\Http\\Requests'),
                'updateRequest' => $this->definition('updateRequest.stub', "app/Http/Requests/{$name}UpdateRequest.php", 'App\\Http\\Requests'),
                'deleteRequest' => $this->definition('deleteRequest.stub', "app/Http/Requests/{$name}DeleteRequest.php", 'App\\Http\\Requests'),
                'readRequest' => $this->definition('readRequest.stub', "app/Http/Requests/{$name}ReadRequest.php", 'App\\Http\\Requests'),
                'test' => $this->definition('test.stub', "tests/Feature/{$name}Test.php", 'Tests\\Feature'),
                'factory' => $this->definition('factory.stub', "database/factories/{$name}Factory.php", 'Database\\Factories'),
                'modelTest' => $this->definition('modelTest.stub', "tests/Unit/{$name}Test.php", 'Tests\\Unit'),
            ];
        }

        $root = $this->targetRoot($target);
        $namespace = $target->namespaceName();

        return [
            'model' => $this->definition('model.stub', "{$root}/Models/{$name}.php", $namespace.'\\Models', true),
            'contract' => $this->definition('contract.stub', "{$root}/Contracts/{$name}RepositoryInterface.php", $namespace.'\\Contracts', true),
            'repository' => $this->definition('repository.stub', "{$root}/Repositories/{$name}Repository.php", $namespace.'\\Repositories', true),
            'service' => $this->definition('service.stub', "{$root}/Services/{$name}Service.php", $namespace.'\\Services', true),
            'controller' => $this->definition('controller.stub', "{$root}/Http/Controllers/{$name}Controller.php", $namespace.'\\Http\\Controllers', true),
            'createRequest' => $this->definition('createRequest.stub', "{$root}/Http/Requests/{$name}CreateRequest.php", $namespace.'\\Http\\Requests', true),
            'updateRequest' => $this->definition('updateRequest.stub', "{$root}/Http/Requests/{$name}UpdateRequest.php", $namespace.'\\Http\\Requests', true),
            'deleteRequest' => $this->definition('deleteRequest.stub', "{$root}/Http/Requests/{$name}DeleteRequest.php", $namespace.'\\Http\\Requests', true),
            'readRequest' => $this->definition('readRequest.stub', "{$root}/Http/Requests/{$name}ReadRequest.php", $namespace.'\\Http\\Requests', true),
            'test' => $this->definition('test.stub', "{$root}/Tests/Feature/{$name}Test.php", $namespace.'\\Tests\\Feature', true),
            'factory' => $this->definition('factory.stub', "{$root}/Database/Factories/{$name}Factory.php", $namespace.'\\Database\\Factories', true),
            'modelTest' => $this->definition('modelTest.stub', "{$root}/Tests/Unit/{$name}Test.php", $namespace.'\\Tests\\Unit', true),
        ];
    }

    private function definition($stub, $relativePath, $namespace, $absolute = false)
    {
        return [
            'stub' => $stub,
            'path' => $absolute ? $relativePath : $this->basePath.'/'.$relativePath,
            'namespace' => $namespace,
        ];
    }

    private function context($name, GenerationTarget $target)
    {
        $variable = lcfirst($name);
        $underscore = Str::snake($variable);
        $rootNamespace = $target->isModular() ? $target->namespaceName() : 'App';

        return [
            'model' => $name,
            'variable' => $variable,
            'underscore' => $underscore,
            'table' => Str::plural($underscore),
            'modelNamespace' => $rootNamespace.'\\Models',
            'contractNamespace' => $rootNamespace.'\\Contracts',
            'repositoryNamespace' => $rootNamespace.'\\Repositories',
            'serviceNamespace' => $rootNamespace.'\\Services',
            'controllerNamespace' => $rootNamespace.'\\Http\\Controllers',
            'requestNamespace' => $rootNamespace.'\\Http\\Requests',
            'factoryNamespace' => $target->isModular() ? $rootNamespace.'\\Database\\Factories' : 'Database\\Factories',
            'featureTestNamespace' => $target->isModular() ? $rootNamespace.'\\Tests\\Feature' : 'Tests\\Feature',
            'unitTestNamespace' => $target->isModular() ? $rootNamespace.'\\Tests\\Unit' : 'Tests\\Unit',
            'providerNamespace' => $target->isModular() ? $rootNamespace.'\\Providers' : 'App\\Providers',
            'modular' => $target->isModular(),
        ];
    }

    private function render($stub, array $context)
    {
        $path = $this->stubPath.'/'.$stub;
        if (! is_file($path)) {
            throw new InvalidArgumentException("Generation stub [{$path}] does not exist.");
        }

        return str_replace(
            ['DummyModel', '{{ model }}', 'DummyModelVariable', '{{ modelVariable }}', 'DummyModelUnderScore', '{{ modelUnderScore }}', '{{ table }}'],
            [$context['model'], $context['model'], $context['variable'], $context['variable'], $context['underscore'], $context['underscore'], $context['table']],
            (string) file_get_contents($path)
        );
    }

    private function localizeNamespaces($content, array $context)
    {
        return strtr($content, [
            'namespace App\\Http\\Controllers;' => 'namespace '.$context['controllerNamespace'].';',
            'namespace App\\Http\\Requests;' => 'namespace '.$context['requestNamespace'].';',
            'namespace App\\Repositories;' => 'namespace '.$context['repositoryNamespace'].';',
            'namespace App\\Contracts;' => 'namespace '.$context['contractNamespace'].';',
            'namespace App\\Services;' => 'namespace '.$context['serviceNamespace'].';',
            'namespace App\\Models;' => 'namespace '.$context['modelNamespace'].';',
            'namespace Database\\Factories;' => 'namespace '.$context['factoryNamespace'].';',
            'namespace Tests\\Feature;' => 'namespace '.$context['featureTestNamespace'].';',
            'namespace Tests\\Unit;' => 'namespace '.$context['unitTestNamespace'].';',
            'App\\Http\\Controllers' => $context['controllerNamespace'],
            'App\\Http\\Requests' => $context['requestNamespace'],
            'App\\Repositories' => $context['repositoryNamespace'],
            'App\\Contracts' => $context['contractNamespace'],
            'App\\Services' => $context['serviceNamespace'],
            'App\\Models' => $context['modelNamespace'],
            'Database\\Factories' => $context['factoryNamespace'],
        ]);
    }

    private function providerContent(array $context, GenerationTarget $target)
    {
        $boot = '';
        if ($target->isModular()) {
            $boot = "\n    public function boot(): void\n    {\n        \$this->loadRoutesFrom(__DIR__.'/../Routes/api.php');\n        \$this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');\n    }\n";
        }

        return "<?php\n\nnamespace {$context['providerNamespace']};\n\nuse {$context['contractNamespace']}\\{$context['model']}RepositoryInterface;\nuse {$context['repositoryNamespace']}\\{$context['model']}Repository;\nuse Illuminate\\Support\\ServiceProvider;\n\nclass {$context['model']}ServiceProvider extends ServiceProvider\n{\n    public function register(): void\n    {\n        \$this->app->bind({$context['model']}RepositoryInterface::class, {$context['model']}Repository::class);\n    }\n{$boot}}\n";
    }

    private function addSharedRepositoryBinding(FeatureGenerationPlan $plan, array $context)
    {
        $providerPath = $this->basePath.'/app/Providers/RepositoryServiceProvider.php';
        $contract = $context['contractNamespace'].'\\'.$context['model'].'RepositoryInterface';
        $repository = $context['repositoryNamespace'].'\\'.$context['model'].'Repository';
        $providerEditor = new RepositoryProviderEditor();

        if (is_file($providerPath)) {
            $original = file_get_contents($providerPath);
            if ($original === false) {
                $plan->addConflict($providerPath);
            } else {
                $result = $providerEditor->update($original, $contract, $repository);
                $result->isSafe()
                    ? $plan->addManagedFile($providerPath, $original, $result->content())
                    : $plan->addConflict($providerPath);
            }
        } elseif (file_exists($providerPath)) {
            $plan->addConflict($providerPath);
        } else {
            $plan->addManagedCreate($providerPath, $providerEditor->create($contract, $repository));
        }

        $bootstrapApplicationPath = $this->basePath.'/bootstrap/app.php';
        if (! is_file($bootstrapApplicationPath)) {
            $plan->addConflict($bootstrapApplicationPath, 'Restore a supported Laravel bootstrap/app.php before provider registration.');
            return;
        }
        $bootstrapApplication = file_get_contents($bootstrapApplicationPath);
        if ($bootstrapApplication === false) {
            $plan->addConflict($bootstrapApplicationPath, 'The application bootstrap could not be read safely.');
            return;
        }
        $plan->addReadDependency($bootstrapApplicationPath, $bootstrapApplication);
        $mode = (new ApplicationBootstrapInspector())->mode($bootstrapApplication);
        if ($mode === ApplicationBootstrapInspector::MODERN) {
            $bootstrapPath = $this->basePath.'/bootstrap/providers.php';
            if (is_file($bootstrapPath)) {
                $original = file_get_contents($bootstrapPath);
                if ($original === false) {
                    $plan->addConflict($bootstrapPath);
                    return;
                }
                $result = (new BootstrapProvidersEditor())->update($original);
                $result->isSafe()
                    ? $plan->addManagedFile($bootstrapPath, $original, $result->content())
                    : $plan->addConflict($bootstrapPath);
                return;
            }
            if (file_exists($bootstrapPath)) {
                $plan->addConflict($bootstrapPath, 'Expected a writable PHP provider-registry file.');
                return;
            }
            $plan->addManagedCreate($bootstrapPath, (new BootstrapProvidersEditor())->create());
            return;
        }
        if ($mode !== ApplicationBootstrapInspector::LEGACY) {
            $plan->addConflict($bootstrapApplicationPath, 'The application bootstrap is not a safely recognized modern or legacy Laravel structure.');
            return;
        }

        $configPath = $this->basePath.'/config/app.php';
        if (! is_file($configPath)) {
            $plan->addConflict($configPath, 'A recognized legacy bootstrap requires a canonical config/app.php provider list.');
            return;
        }
        $original = file_get_contents($configPath);
        if ($original === false) {
            $plan->addConflict($configPath);
            return;
        }
        $result = (new LegacyConfigProvidersEditor())->update($original);
        $result->isSafe()
            ? $plan->addManagedFile($configPath, $original, $result->content())
            : $plan->addConflict($configPath, 'Use one canonical ServiceProvider::defaultProviders()->merge([...])->toArray() list with unambiguous class literals.');
    }

    private function addModularBaseImports($content, $artifact, array $context)
    {
        if (! $context['modular']) {
            return $content;
        }

        $imports = [
            'service' => 'use App\\Services\\BasicCrudService;',
            'controller' => 'use App\\Http\\Controllers\\Controller;',
            'createRequest' => 'use App\\Http\\Requests\\BaseFormRequest;',
            'updateRequest' => 'use App\\Http\\Requests\\BaseFormRequest;',
            'deleteRequest' => 'use App\\Http\\Requests\\BaseFormRequest;',
            'readRequest' => 'use App\\Http\\Requests\\BaseFormRequest;',
        ];

        if (! isset($imports[$artifact])) {
            return $content;
        }

        $namespace = $this->namespaceForArtifact($artifact, $context);

        return str_replace(
            'namespace '.$namespace.';',
            'namespace '.$namespace.';'.PHP_EOL.PHP_EOL.$imports[$artifact],
            $content
        );
    }

    private function namespaceForArtifact($artifact, array $context)
    {
        if ($artifact === 'service') {
            return $context['serviceNamespace'];
        }
        if ($artifact === 'controller') {
            return $context['controllerNamespace'];
        }

        return $context['requestNamespace'];
    }

    private function migrationPath($table, GenerationTarget $target)
    {
        $directory = $target->isModular()
            ? $this->targetRoot($target).'/Database/Migrations'
            : $this->basePath.'/database/migrations';
        $matches = glob($directory.'/*_create_'.$table.'_table.php');

        return $matches !== false && $matches !== []
            ? $matches[0]
            : $directory.'/'.date('Y_m_d_His')."_create_{$table}_table.php";
    }

    private function targetRoot(GenerationTarget $target)
    {
        return $this->basePath.'/'.$target->path();
    }

    private function validateName($name)
    {
        if (! is_string($name) || preg_match('/^[A-Z][A-Za-z0-9]*$/', $name) !== 1) {
            throw new InvalidArgumentException('Feature names must be a single PascalCase PHP identifier.');
        }
    }

    private function validateNamespace($content, $namespace, $stub)
    {
        if (strpos($content, 'namespace '.$namespace.';') === false) {
            throw new InvalidArgumentException("Generation stub [{$stub}] does not declare the expected [{$namespace}] namespace.");
        }
    }
}
