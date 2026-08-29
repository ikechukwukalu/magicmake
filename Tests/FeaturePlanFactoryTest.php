<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Ikechukwukalu\Magicmake\Generation\GenerationConflictException;
use Ikechukwukalu\Magicmake\Generation\GenerationProfile;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class FeaturePlanFactoryTest extends TestCase
{
    /** @var string */
    private $path;

    /** @var FeaturePlanFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/magicmake-feature-'.uniqid('', true);
        mkdir($this->path.'/routes', 0755, true);
        file_put_contents($this->path.'/routes/api.php', "<?php\n");
        file_put_contents($this->path.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\\' => 'app/']],
            'autoload-dev' => ['psr-4' => ['Tests\\' => 'tests/']],
        ]));
        $this->factory = new FeaturePlanFactory(
            $this->path,
            dirname(__DIR__).'/src/Console/Commands/stubs'
        );
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->path);
    }

    public function test_plan_contains_every_feature_artifact_before_commit(): void
    {
        $preview = $this->factory->make('DeliveryType')->preview();

        $this->assertCount(13, $preview);
        $this->assertSame(['create', 'append'], array_values(array_unique(array_column($preview, 'action'))));
    }

    public function test_profiles_have_explicit_artifact_contracts(): void
    {
        $lean = $this->factory->make('Country', GenerationProfile::LEAN);
        $standard = $this->factory->make('DeliveryType', GenerationProfile::STANDARD);
        $enterprise = $this->factory->make('Warehouse', GenerationProfile::ENTERPRISE);

        $this->assertSame(['model', 'migration', 'factory', 'modelTest'], $lean->artifacts());
        $this->assertCount(4, $lean->preview());
        $this->assertCount(13, $standard->preview());
        $this->assertCount(14, $enterprise->preview());
        $this->assertContains($this->path.'/app/Providers/WarehouseServiceProvider.php', array_column($enterprise->preview(), 'path'));
    }

    public function test_target_path_derives_namespace_and_keeps_standard_output_in_boundary(): void
    {
        $plan = $this->factory->make('Invoice', GenerationProfile::STANDARD, 'app/Domains/Billing');

        $this->assertSame('app/Domains/Billing', $plan->target()->path());
        $this->assertSame('App\\Domains\\Billing', $plan->target()->namespaceName());
        foreach ($plan->preview() as $item) {
            $this->assertStringStartsWith($this->path.'/app/Domains/Billing/', $item['path']);
        }

        $plan->commit();
        $this->assertPhpFilesParse($plan->preview());

        $this->assertStringContainsString('namespace App\\Domains\\Billing\\Models;', file_get_contents($this->path.'/app/Domains/Billing/Models/Invoice.php'));
        $this->assertStringContainsString('App\\Domains\\Billing\\Database\\Factories\\InvoiceFactory::new()', file_get_contents($this->path.'/app/Domains/Billing/Models/Invoice.php'));
        $this->assertStringContainsString('use App\\Services\\BasicCrudService;', file_get_contents($this->path.'/app/Domains/Billing/Services/InvoiceService.php'));
        $this->assertStringContainsString('use App\\Http\\Controllers\\Controller;', file_get_contents($this->path.'/app/Domains/Billing/Http/Controllers/InvoiceController.php'));
        $this->assertStringContainsString('use App\\Http\\Requests\\BaseFormRequest;', file_get_contents($this->path.'/app/Domains/Billing/Http/Requests/InvoiceCreateRequest.php'));
        $this->assertStringContainsString('protected string|null $responseMode = ResponseMode::JSON;', file_get_contents($this->path.'/app/Domains/Billing/Http/Controllers/InvoiceController.php'));
        $this->assertStringStartsWith('<?php', file_get_contents($this->path.'/app/Domains/Billing/Routes/api.php'));
    }

    public function test_enterprise_provider_registers_modular_dependencies(): void
    {
        $plan = $this->factory->make('Invoice', GenerationProfile::ENTERPRISE, null, 'App\\Domains\\Billing');
        $plan->commit();
        $this->assertPhpFilesParse($plan->preview());

        $provider = file_get_contents($this->path.'/app/Domains/Billing/Providers/InvoiceServiceProvider.php');
        $this->assertStringContainsString('InvoiceRepositoryInterface::class, InvoiceRepository::class', $provider);
        $this->assertStringContainsString("loadRoutesFrom(__DIR__.'/../Routes/api.php')", $provider);
        $this->assertStringContainsString("loadMigrationsFrom(__DIR__.'/../Database/Migrations')", $provider);
    }

    public function test_path_and_namespace_mismatch_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->make('Invoice', GenerationProfile::STANDARD, 'app/Domains/Billing', 'App\\Domains\\Shipping');
    }

    public function test_unmapped_target_path_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->make('Invoice', GenerationProfile::STANDARD, 'modules/Billing');
    }

    public function test_one_conflict_prevents_the_entire_feature(): void
    {
        mkdir($this->path.'/app/Services', 0755, true);
        file_put_contents($this->path.'/app/Services/DeliveryTypeService.php', 'custom');

        try {
            $this->factory->make('DeliveryType')->commit();
            $this->fail('Expected a conflict.');
        } catch (GenerationConflictException $exception) {
            $this->assertContains($this->path.'/app/Services/DeliveryTypeService.php', $exception->conflicts());
        }

        $this->assertFileDoesNotExist($this->path.'/app/Models/DeliveryType.php');
        $this->assertSame('custom', file_get_contents($this->path.'/app/Services/DeliveryTypeService.php'));
    }

    public function test_invalid_names_are_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->factory->make('../DeliveryType');
    }

    private function removeDirectory($directory)
    {
        if (! is_dir($directory)) {
            return;
        }

        foreach (array_diff(scandir($directory), ['.', '..']) as $item) {
            $path = $directory.'/'.$item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }

        rmdir($directory);
    }

    private function assertPhpFilesParse(array $preview)
    {
        foreach (array_column($preview, 'path') as $path) {
            if (substr($path, -4) !== '.php') {
                continue;
            }

            token_get_all(file_get_contents($path), TOKEN_PARSE);
            $this->addToAssertionCount(1);
        }
    }
}
