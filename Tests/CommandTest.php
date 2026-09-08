<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Console\Commands\MagicModelCommand;
use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Ikechukwukalu\Magicmake\Response\ResponseMode;
use Illuminate\Contracts\Console\Kernel;
use ReflectionClass;

class CommandTest extends TestCase
{
    /** @var string */
    private $projectPath;

    public function setUp(): void
    {
        parent::setUp();

        $this->projectPath = sys_get_temp_dir().'/magicmake-command-'.uniqid('', true);
        mkdir($this->projectPath.'/routes', 0755, true);
        mkdir($this->projectPath.'/bootstrap', 0755, true);
        file_put_contents($this->projectPath.'/routes/api.php', "<?php\n");
        file_put_contents($this->projectPath.'/routes/web.php', "<?php\n");
        file_put_contents($this->projectPath.'/bootstrap/app.php', "<?php\n\nuse Illuminate\\Foundation\\Application;\n\nreturn Application::configure(basePath: dirname(__DIR__))\n    ->withRouting(web: __DIR__.'/../routes/web.php')\n    ->create();\n");
        file_put_contents($this->projectPath.'/bootstrap/providers.php', "<?php\n\nreturn [\n    App\\Providers\\AppServiceProvider::class,\n];\n");
        file_put_contents($this->projectPath.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\\' => 'app/']],
            'autoload-dev' => ['psr-4' => ['Tests\\' => 'tests/']],
        ]));
        $this->app->setBasePath($this->projectPath);
        $this->app->detectEnvironment(function () {
            return 'local';
        });
        $this->app['config']->set('magicmake.init_lock', false);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->removeDirectory($this->projectPath);
    }

    public function test_model_command_generates_complete_feature_and_rerun_is_non_destructive(): void
    {
        $this->artisan('magic:model DeliveryType')->assertSuccessful();

        $this->assertFileExists($this->projectPath.'/app/Models/DeliveryType.php');
        $this->assertFileExists($this->projectPath.'/app/Services/DeliveryTypeService.php');
        $this->assertFileExists($this->projectPath.'/tests/Feature/DeliveryTypeTest.php');
        $this->assertCount(1, glob($this->projectPath.'/database/migrations/*_create_delivery_types_table.php'));

        $modelContents = file_get_contents($this->projectPath.'/app/Models/DeliveryType.php');
        $this->artisan('magic:model DeliveryType')->assertSuccessful();
        $this->assertSame($modelContents, file_get_contents($this->projectPath.'/app/Models/DeliveryType.php'));
        $this->assertSame(1, substr_count(file_get_contents($this->projectPath.'/routes/api.php'), "Route::prefix('delivery_type')"));
    }

    public function test_model_command_remains_compatible_with_legacy_initialized_scaffolding(): void
    {
        mkdir($this->projectPath.'/app/Http/Controllers', 0755, true);
        $legacyController = <<<'PHP'
<?php

namespace App\Http\Controllers;

class Controller
{
    protected function _create(mixed $request, mixed $service, string|null $component = null) {}
    protected function _update(mixed $request, mixed $service, string|null $component = null) {}
    protected function _delete(mixed $request, mixed $service, string|null $component = null) {}
    protected function _read(mixed $request, mixed $service, null|string|int $id = null, string|null $component = null) {}
}
PHP;
        file_put_contents($this->projectPath.'/app/Http/Controllers/Controller.php', $legacyController);

        $legacyHelpers = <<<'PHP'
<?php

function httpResponseFormat(mixed $request, mixed $response, string|null $component = null) {}
function unknownErrorResponseFormat(mixed $request) {}
PHP;
        file_put_contents($this->projectPath.'/app/Http/Helpers.php', $legacyHelpers);

        $this->artisan('magic:model ExistingProjectOrder')->assertSuccessful();

        $this->assertSame($legacyController, file_get_contents($this->projectPath.'/app/Http/Controllers/Controller.php'));
        $this->assertSame($legacyHelpers, file_get_contents($this->projectPath.'/app/Http/Helpers.php'));
        $this->assertFileDoesNotExist($this->projectPath.'/config/magicmake.php');

        $generatedController = $this->projectPath.'/app/Http/Controllers/ExistingProjectOrderController.php';
        $contents = file_get_contents($generatedController);
        token_get_all($contents, TOKEN_PARSE);
        $this->addToAssertionCount(1);
        $this->assertStringContainsString('protected string|null $responseMode = ResponseMode::JSON;', $contents);
        $this->assertStringContainsString('return $this->_create($request, $this->existingProjectOrderService);', $contents);

        require_once $this->projectPath.'/app/Http/Controllers/Controller.php';
        require_once $generatedController;
        $reflection = new ReflectionClass('App\\Http\\Controllers\\ExistingProjectOrderController');
        $this->assertTrue($reflection->isSubclassOf('App\\Http\\Controllers\\Controller'));
        $this->assertSame(ResponseMode::JSON, $reflection->getDefaultProperties()['responseMode']);
    }

    public function test_init_is_idempotent_and_does_not_duplicate_routes(): void
    {
        $this->artisan('magic:init')->assertSuccessful();
        $responseData = $this->projectPath.'/app/Actions/ResponseData.php';
        $original = file_get_contents($responseData);

        $this->artisan('magic:init')->assertSuccessful();

        $this->assertSame($original, file_get_contents($responseData));
        $this->assertSame(1, substr_count(file_get_contents($this->projectPath.'/routes/api.php'), "require __DIR__.'/app/user.php'"));
        $this->assertFileExists($this->projectPath.'/config/magicmake.php');
        $this->assertStringContainsString("'response_mode' => env('MAGIC_RESPONSE_MODE', 'auto')", file_get_contents($this->projectPath.'/config/magicmake.php'));
        $this->assertStringContainsString('new ResponsePresenter', file_get_contents($this->projectPath.'/app/Http/Helpers.php'));
        token_get_all(file_get_contents($this->projectPath.'/app/Http/Helpers.php'), TOKEN_PARSE);
        token_get_all(file_get_contents($this->projectPath.'/app/Http/Controllers/Controller.php'), TOKEN_PARSE);
        $this->addToAssertionCount(2);
    }

    public function test_init_preserves_customized_files_until_force_is_explicit(): void
    {
        $this->artisan('magic:init')->assertSuccessful();
        $responseData = $this->projectPath.'/app/Actions/ResponseData.php';
        $generated = file_get_contents($responseData);
        file_put_contents($responseData, 'customized');

        $this->artisan('magic:init')->assertFailed();
        $this->assertSame('customized', file_get_contents($responseData));

        $this->artisan('magic:init --force')->assertSuccessful();
        $this->assertSame($generated, file_get_contents($responseData));
    }

    public function test_dry_run_does_not_write_feature_files(): void
    {
        $this->artisan('magic:model DeliveryType --dry-run')->assertSuccessful();

        $this->assertFileDoesNotExist($this->projectPath.'/app/Models/DeliveryType.php');
        $this->assertSame("<?php\n", file_get_contents($this->projectPath.'/routes/api.php'));
    }

    public function test_lean_modular_profile_uses_derived_namespace(): void
    {
        $this->artisan('magic:model Invoice --profile=lean --path=app/Domains/Billing')->assertSuccessful();

        $this->assertFileExists($this->projectPath.'/app/Domains/Billing/Models/Invoice.php');
        $this->assertFileExists($this->projectPath.'/app/Domains/Billing/Tests/Unit/InvoiceTest.php');
        $this->assertFileDoesNotExist($this->projectPath.'/app/Domains/Billing/Contracts/InvoiceRepositoryInterface.php');
    }

    public function test_invalid_profile_fails_without_writes(): void
    {
        $this->artisan('magic:model Invoice --profile=custom')->assertFailed();
        $this->assertFileDoesNotExist($this->projectPath.'/app/Models/Invoice.php');
    }

    public function test_model_help_lists_repository_profile(): void
    {
        $this->artisan('help magic:model')
            ->expectsOutputToContain('lean, repository, standard, or enterprise')
            ->assertSuccessful();
    }

    public function test_repository_profile_generates_array_based_service_and_registers_binding(): void
    {
        $this->artisan('magic:model Invoice --profile=repository')->assertSuccessful();

        $service = file_get_contents($this->projectPath.'/app/Services/InvoiceService.php');
        $provider = file_get_contents($this->projectPath.'/app/Providers/RepositoryServiceProvider.php');
        $bootstrap = file_get_contents($this->projectPath.'/bootstrap/providers.php');

        $this->assertStringContainsString('handleCreate(array $data): ResponseData', $service);
        $this->assertStringContainsString('handleUpdate(array $data): ResponseData', $service);
        $this->assertStringContainsString('handleDelete(int|string $id): ResponseData', $service);
        $this->assertStringNotContainsString('Http\\Requests', $service);
        $this->assertStringContainsString('\\App\\Contracts\\InvoiceRepositoryInterface::class, \\App\\Repositories\\InvoiceRepository::class', $provider);
        $this->assertSame(1, substr_count($bootstrap, 'App\\Providers\\RepositoryServiceProvider::class'));
    }

    public function test_repository_profile_supports_an_established_laravel_twelve_legacy_provider_registry(): void
    {
        unlink($this->projectPath.'/bootstrap/providers.php');
        file_put_contents($this->projectPath.'/bootstrap/app.php', <<<'PHP'
<?php
$app = new Illuminate\Foundation\Application($_ENV['APP_BASE_PATH'] ?? dirname(__DIR__));
$app->singleton(Illuminate\Contracts\Http\Kernel::class, App\Http\Kernel::class);
return $app;
PHP);
        mkdir($this->projectPath.'/config', 0755, true);
        $config = <<<'PHP'
<?php

use Illuminate\Support\ServiceProvider;

return [
    'providers' => ServiceProvider::defaultProviders()->merge([
        // Existing application provider registration.
        App\Providers\RepositoryServiceProvider::class,
    ])->toArray(),
];
PHP;
        file_put_contents($this->projectPath.'/config/app.php', $config);

        $this->artisan('magic:model UserExportInvoice --profile=repository')->assertSuccessful();

        $this->assertSame($config, file_get_contents($this->projectPath.'/config/app.php'));
        $this->assertFileDoesNotExist($this->projectPath.'/bootstrap/providers.php');
        $this->assertFileExists($this->projectPath.'/app/Models/UserExportInvoice.php');
        $this->assertFileExists($this->projectPath.'/app/Contracts/UserExportInvoiceRepositoryInterface.php');
        $this->assertFileExists($this->projectPath.'/app/Repositories/UserExportInvoiceRepository.php');
        $this->assertFileExists($this->projectPath.'/app/Services/UserExportInvoiceService.php');
        $this->assertFileExists($this->projectPath.'/app/Providers/RepositoryServiceProvider.php');
    }

    public function test_repository_command_aborts_if_modern_bootstrap_changes_after_planning(): void
    {
        $changed = "<?php\n\n// application-owned modern bootstrap change\n";
        $command = $this->registerBootstrapMutationCommand('magic:model-modern-race', $changed);

        $this->artisan($command.' ModernRace --profile=repository')
            ->expectsOutputToContain('bootstrap/app.php] changed after preflight')
            ->assertFailed();

        $this->assertSame($changed, file_get_contents($this->projectPath.'/bootstrap/app.php'));
        $this->assertStringNotContainsString('RepositoryServiceProvider', file_get_contents($this->projectPath.'/bootstrap/providers.php'));
        $this->assertFileDoesNotExist($this->projectPath.'/app/Models/ModernRace.php');
        $this->assertFileDoesNotExist($this->projectPath.'/app/Providers/RepositoryServiceProvider.php');
    }

    public function test_repository_command_aborts_if_legacy_bootstrap_changes_after_planning(): void
    {
        unlink($this->projectPath.'/bootstrap/providers.php');
        file_put_contents($this->projectPath.'/bootstrap/app.php', "<?php\n\n\$app = new Illuminate\\Foundation\\Application(dirname(__DIR__));\nreturn \$app;\n");
        mkdir($this->projectPath.'/config', 0755, true);
        $config = "<?php\n\nuse Illuminate\\Support\\ServiceProvider;\n\nreturn [\n    'providers' => ServiceProvider::defaultProviders()->merge([\n        App\\Providers\\AppServiceProvider::class,\n    ])->toArray(),\n];\n";
        file_put_contents($this->projectPath.'/config/app.php', $config);
        $changed = "<?php\n\n// application-owned legacy bootstrap change\n";
        $command = $this->registerBootstrapMutationCommand('magic:model-legacy-race', $changed);

        $this->artisan($command.' LegacyRace --profile=repository')
            ->expectsOutputToContain('bootstrap/app.php] changed after preflight')
            ->assertFailed();

        $this->assertSame($changed, file_get_contents($this->projectPath.'/bootstrap/app.php'));
        $this->assertSame($config, file_get_contents($this->projectPath.'/config/app.php'));
        $this->assertFileDoesNotExist($this->projectPath.'/app/Models/LegacyRace.php');
        $this->assertFileDoesNotExist($this->projectPath.'/app/Providers/RepositoryServiceProvider.php');
    }

    private function registerBootstrapMutationCommand($name, $changedBootstrap)
    {
        $factory = new FeaturePlanFactory($this->projectPath, dirname(__DIR__).'/src/Console/Commands/stubs');
        $bootstrapPath = $this->projectPath.'/bootstrap/app.php';
        $mutatingFactory = new class($factory, $bootstrapPath, $changedBootstrap) {
            private $factory;
            private $bootstrapPath;
            private $changedBootstrap;

            public function __construct($factory, $bootstrapPath, $changedBootstrap)
            {
                $this->factory = $factory;
                $this->bootstrapPath = $bootstrapPath;
                $this->changedBootstrap = $changedBootstrap;
            }

            public function make($model, $profile, $path, $namespace)
            {
                $plan = $this->factory->make($model, $profile, $path, $namespace);
                file_put_contents($this->bootstrapPath, $this->changedBootstrap);

                return $plan;
            }
        };
        $command = new class($mutatingFactory) extends MagicModelCommand {
            private $testFactory;

            public function __construct($factory)
            {
                $this->testFactory = $factory;
                parent::__construct();
            }

            protected function factory()
            {
                return $this->testFactory;
            }
        };
        $command->setName($name);
        $this->app->make(Kernel::class)->registerCommand($command);

        return $name;
    }

    private function removeDirectory($directory)
    {
        if (! is_dir($directory)) {
            return;
        }

        $items = array_diff(scandir($directory), ['.', '..']);
        foreach ($items as $item) {
            $path = $directory.'/'.$item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }
}
