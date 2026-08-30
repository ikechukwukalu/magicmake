<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Response\ResponseMode;
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
        file_put_contents($this->projectPath.'/routes/api.php', "<?php\n");
        file_put_contents($this->projectPath.'/routes/web.php', "<?php\n");
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
