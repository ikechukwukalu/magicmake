<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Ikechukwukalu\Magicmake\Generation\GenerationConflictException;
use Ikechukwukalu\Magicmake\Generation\GenerationProfile;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RepositoryProfileTest extends TestCase
{
    private $path;
    private $factory;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/magicmake-repository-'.uniqid('', true);
        mkdir($this->path.'/routes', 0755, true);
        mkdir($this->path.'/bootstrap', 0755, true);
        file_put_contents($this->path.'/routes/api.php', "<?php\n");
        file_put_contents($this->path.'/bootstrap/app.php', "<?php\n\nuse Illuminate\\Foundation\\Application;\n\nreturn Application::configure(basePath: dirname(__DIR__))\n    ->withRouting(web: __DIR__.'/../routes/web.php')\n    ->create();\n");
        file_put_contents($this->path.'/bootstrap/providers.php', "<?php\n\nreturn [\n    App\\Providers\\AppServiceProvider::class,\n];\n");
        file_put_contents($this->path.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\\' => 'app/']],
            'autoload-dev' => ['psr-4' => ['Tests\\' => 'tests/']],
        ]));
        $this->factory = new FeaturePlanFactory($this->path, dirname(__DIR__).'/src/Console/Commands/stubs');
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->path);
    }

    public function test_repository_profile_has_exact_artifacts_and_parseable_service(): void
    {
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertSame(
            ['model', 'migration', 'factory', 'modelTest', 'contract', 'repository', 'service'],
            $plan->artifacts()
        );
        $plan->commit();

        $service = file_get_contents($this->path.'/app/Services/InvoiceService.php');
        token_get_all($service, TOKEN_PARSE);
        $this->assertStringContainsString('extends BasicCrudService', $service);
        $this->assertStringContainsString('private InvoiceRepositoryInterface $invoiceRepository', $service);
        $this->assertStringContainsString('return $this->create($data, $this->invoiceRepository);', $service);
        $this->assertStringContainsString('return $this->delete([\'id\' => $id], $this->invoiceRepository);', $service);
        $this->assertStringNotContainsString('Request', $service);
        $this->assertFileDoesNotExist($this->path.'/app/Http/Controllers/InvoiceController.php');
    }

    public function test_shared_provider_supports_multiple_features_and_same_basename_modules(): void
    {
        $this->factory->make('Invoice', GenerationProfile::STANDARD)->commit();
        $this->factory->make('Order', GenerationProfile::REPOSITORY)->commit();
        $this->factory->make('Invoice', GenerationProfile::REPOSITORY, 'app/Domains/Billing')->commit();
        $this->factory->make('Invoice', GenerationProfile::REPOSITORY, 'app/Domains/Shipping')->commit();

        $provider = file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php');
        $this->assertSame(4, substr_count($provider, '$this->app->bind('));
        $this->assertStringContainsString('\\App\\Domains\\Billing\\Contracts\\InvoiceRepositoryInterface::class', $provider);
        $this->assertStringContainsString('\\App\\Domains\\Shipping\\Contracts\\InvoiceRepositoryInterface::class', $provider);
        $this->assertSame(1, substr_count(file_get_contents($this->path.'/bootstrap/providers.php'), 'RepositoryServiceProvider::class'));
        token_get_all($provider, TOKEN_PARSE);
    }

    public function test_exact_rerun_is_idempotent(): void
    {
        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $provider = file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php');
        $bootstrap = file_get_contents($this->path.'/bootstrap/providers.php');

        $preview = $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->preview();
        $this->assertNotContains('conflict', array_column($preview, 'action'));
        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $this->assertSame($provider, file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php'));
        $this->assertSame($bootstrap, file_get_contents($this->path.'/bootstrap/providers.php'));
    }

    public function test_existing_unrelated_provider_content_is_preserved(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $existing = <<<'PHP'
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Keep this application binding.
        $this->app->singleton(Clock::class, SystemClock::class);
    }
}
PHP;
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $existing);

        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $updated = file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php');
        $this->assertStringContainsString('// Keep this application binding.', $updated);
        $this->assertStringContainsString('$this->app->singleton(Clock::class, SystemClock::class);', $updated);
    }

    public function test_existing_short_name_binding_and_registration_are_recognized(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use App\Contracts\InvoiceRepositoryInterface;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
    }
}
PHP;
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);
        file_put_contents($this->path.'/bootstrap/providers.php', "<?php\nreturn [\n    App\\Providers\\RepositoryServiceProvider::class,\n];\n");

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $plan->commit();
        $this->assertSame($provider, file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php'));
        $this->assertSame(1, substr_count(file_get_contents($this->path.'/bootstrap/providers.php'), 'RepositoryServiceProvider::class'));
    }

    public function test_grouped_provider_import_and_aliased_bootstrap_registration_are_recognized(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use App\Contracts\{InvoiceRepositoryInterface};
use App\Repositories\{InvoiceRepository};
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(InvoiceRepositoryInterface::class, InvoiceRepository::class);
    }
}
PHP;
        $bootstrap = "<?php\nuse App\\Providers\\RepositoryServiceProvider as Repositories;\nreturn [\n    Repositories::class,\n];\n";
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);
        file_put_contents($this->path.'/bootstrap/providers.php', $bootstrap);

        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $this->assertSame($provider, file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php'));
        $this->assertSame($bootstrap, file_get_contents($this->path.'/bootstrap/providers.php'));
    }

    public function test_generated_provider_binding_resolves_in_the_container(): void
    {
        $this->factory->make('ContainerInvoice', GenerationProfile::REPOSITORY)->commit();
        require_once $this->path.'/app/Models/ContainerInvoice.php';
        require_once $this->path.'/app/Contracts/ContainerInvoiceRepositoryInterface.php';
        require_once $this->path.'/app/Repositories/ContainerInvoiceRepository.php';
        require_once $this->path.'/app/Providers/RepositoryServiceProvider.php';

        $container = new \Illuminate\Container\Container();
        $provider = new \App\Providers\RepositoryServiceProvider($container);
        $provider->register();

        $resolved = $container->make(\App\Contracts\ContainerInvoiceRepositoryInterface::class);
        $this->assertInstanceOf(\App\Repositories\ContainerInvoiceRepository::class, $resolved);
    }

    public function test_dry_run_exposes_infrastructure_without_writing_and_force_cannot_hide_it(): void
    {
        $plan = $this->factory->make('PreviewInvoice', GenerationProfile::REPOSITORY);
        $preview = $plan->preview(true);
        $this->assertContains($this->path.'/app/Providers/RepositoryServiceProvider.php', array_column($preview, 'path'));
        $this->assertContains($this->path.'/bootstrap/providers.php', array_column($preview, 'path'));
        $this->assertFileDoesNotExist($this->path.'/app/Providers/RepositoryServiceProvider.php');
        $this->assertStringNotContainsString('RepositoryServiceProvider', file_get_contents($this->path.'/bootstrap/providers.php'));
    }

    public function test_duplicate_bootstrap_registration_is_a_non_overridable_conflict(): void
    {
        file_put_contents($this->path.'/bootstrap/providers.php', "<?php\nreturn [\n    App\\Providers\\RepositoryServiceProvider::class,\n    App\\Providers\\RepositoryServiceProvider::class,\n];\n");
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($this->path.'/bootstrap/providers.php', $plan->conflicts(true));
    }

    public function test_malformed_repository_provider_is_a_non_overridable_conflict(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = "<?php\nnamespace App\\Providers;\nclass RepositoryServiceProvider {}\n";
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($this->path.'/app/Providers/RepositoryServiceProvider.php', $plan->conflicts(true));
    }

    public function test_unwritable_bootstrap_provider_is_a_preflight_conflict(): void
    {
        chmod($this->path.'/bootstrap/providers.php', 0444);
        try {
            $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
            $this->assertContains($this->path.'/bootstrap/providers.php', $plan->conflicts());
        } finally {
            chmod($this->path.'/bootstrap/providers.php', 0644);
        }
    }

    public function test_conflicting_binding_fails_even_with_force_without_writes(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\LegacyInvoiceRepository::class);
    }
}
PHP;
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($this->path.'/app/Providers/RepositoryServiceProvider.php', $plan->conflicts(true));
        try {
            $plan->commit(true);
            $this->fail('Expected provider conflict.');
        } catch (GenerationConflictException $exception) {
            $this->assertContains($this->path.'/app/Providers/RepositoryServiceProvider.php', $exception->conflicts());
        }
        $this->assertSame($provider, file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php'));
        $this->assertFileDoesNotExist($this->path.'/app/Models/Invoice.php');
    }

    public function test_unrelated_receiver_binding_does_not_satisfy_application_binding(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $other->bind(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\InvoiceRepository::class);
    }
}
PHP;
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);

        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $updated = file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php');
        $this->assertStringContainsString('$other->bind(', $updated);
        $this->assertSame(1, substr_count($updated, '$this->app->bind('));
    }

    public function test_exact_app_helper_binding_is_preserved(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        app()->bind(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\InvoiceRepository::class);
    }
}
PHP;
        file_put_contents($providerPath, $provider);

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $providerPreview = array_values(array_filter($plan->preview(), function ($item) {
            return basename($item['path']) === 'RepositoryServiceProvider.php';
        }));
        $this->assertSame('skip', $providerPreview[0]['action']);
        $plan->commit();
        $this->assertSame($provider, file_get_contents($providerPath));
    }

    public function test_conflicting_app_helper_registration_is_non_forceable(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        app()->singleton(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\LegacyInvoiceRepository::class);
    }
}
PHP;
        file_put_contents($providerPath, $provider);

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($providerPath, $plan->conflicts(true));
        $this->assertSame($provider, file_get_contents($providerPath));
    }

    public function test_named_binding_arguments_are_resolved_by_signature_and_order(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $exactRegistrations = [
            '$this->app->bind(abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class, concrete: \\App\\Repositories\\InvoiceRepository::class);',
            '$this->app->bind(concrete: \\App\\Repositories\\InvoiceRepository::class, abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class);',
            'app()->bind(\\App\\Contracts\\InvoiceRepositoryInterface::class, concrete: \\App\\Repositories\\InvoiceRepository::class);',
            'app()->singleton(concrete: \\App\\Repositories\\InvoiceRepository::class, abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class);',
            '$this->app->instance(instance: new \\App\\Repositories\\InvoiceRepository(), abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class);',
        ];

        foreach ($exactRegistrations as $registration) {
            $provider = $this->providerWithRegistration($registration);
            file_put_contents($providerPath, $provider);
            $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
            $providerPreview = array_values(array_filter($plan->preview(), function ($item) {
                return basename($item['path']) === 'RepositoryServiceProvider.php';
            }));
            $this->assertSame('skip', $providerPreview[0]['action'], $registration);
        }
    }

    public function test_named_binding_conflicts_and_ambiguous_targets_are_non_forceable(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $registrations = [
            '$this->app->bind(abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class, concrete: \\App\\Repositories\\LegacyInvoiceRepository::class);',
            'app()->singleton(concrete: \\App\\Repositories\\LegacyInvoiceRepository::class, abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class);',
            'app()->instance(instance: new \\App\\Repositories\\LegacyInvoiceRepository(), abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class);',
            '$this->app->bind($condition ? \\App\\Contracts\\InvoiceRepositoryInterface::class : \\App\\Contracts\\OtherRepositoryInterface::class, \\App\\Repositories\\InvoiceRepository::class);',
            '$this->app->bind(abstract: $condition ? \\App\\Contracts\\InvoiceRepositoryInterface::class : \\App\\Contracts\\OtherRepositoryInterface::class, concrete: \\App\\Repositories\\InvoiceRepository::class);',
            'app()->bind(abstract: \\App\\Contracts\\InvoiceRepositoryInterface::class, concrete: $condition ? \\App\\Repositories\\InvoiceRepository::class : \\App\\Repositories\\LegacyInvoiceRepository::class);',
            'app()->bind(target: \\App\\Contracts\\InvoiceRepositoryInterface::class, concrete: \\App\\Repositories\\InvoiceRepository::class);',
        ];

        foreach ($registrations as $registration) {
            $provider = $this->providerWithRegistration($registration);
            file_put_contents($providerPath, $provider);
            $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
            $this->assertContains($providerPath, $plan->conflicts(true), $registration);
            $this->assertSame($provider, file_get_contents($providerPath));
        }
    }

    public function test_conflicting_singleton_binding_fails_even_with_force(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\LegacyInvoiceRepository::class);
    }
}
PHP;
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($this->path.'/app/Providers/RepositoryServiceProvider.php', $plan->conflicts(true));
        $this->assertSame($provider, file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php'));
    }

    public function test_exact_container_lifecycle_bindings_are_preserved_as_application_intent(): void
    {
        foreach (['bind', 'bindIf', 'singleton', 'singletonIf', 'scoped', 'scopedIf'] as $method) {
            if (! is_dir($this->path.'/app/Providers')) {
                mkdir($this->path.'/app/Providers', 0755, true);
            }
            $provider = "<?php\nnamespace App\\Providers;\nuse Illuminate\\Support\\ServiceProvider;\nclass RepositoryServiceProvider extends ServiceProvider\n{\n    public function register(): void\n    {\n        \$this->app->{$method}(\\App\\Contracts\\InvoiceRepositoryInterface::class, \\App\\Repositories\\InvoiceRepository::class);\n    }\n}\n";
            file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);

            $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
            $providerPreview = array_values(array_filter($plan->preview(), function ($item) {
                return basename($item['path']) === 'RepositoryServiceProvider.php';
            }));
            $this->assertCount(1, $providerPreview);
            $this->assertSame('skip', $providerPreview[0]['action'], $method);
        }
    }

    public function test_malformed_bootstrap_provider_file_prevents_all_writes(): void
    {
        foreach (["<?php\nreturn collect([]);\n", "<?php\nreturn [;\n"] as $index => $content) {
            file_put_contents($this->path.'/bootstrap/providers.php', $content);

            $plan = $this->factory->make('Invoice'.$index, GenerationProfile::REPOSITORY);
            $this->assertContains($this->path.'/bootstrap/providers.php', $plan->conflicts(true));
            try {
                $plan->commit(true);
                $this->fail('Expected bootstrap provider conflict.');
            } catch (GenerationConflictException $exception) {
                $this->assertContains($this->path.'/bootstrap/providers.php', $exception->conflicts());
            }
            $this->assertFileDoesNotExist($this->path.'/app/Models/Invoice'.$index.'.php');
        }
    }

    public function test_injected_failure_restores_provider_and_bootstrap_byte_for_byte(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = "<?php\nnamespace App\\Providers;\nuse Illuminate\\Support\\ServiceProvider;\nclass RepositoryServiceProvider extends ServiceProvider { public function register(): void { /* original */ } }\n";
        $bootstrap = file_get_contents($this->path.'/bootstrap/providers.php');
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);

        try {
            $plan->commit(false, function ($path) {
                if (substr($path, -23) === 'bootstrap/providers.php') {
                    throw new RuntimeException('Injected failure.');
                }
            });
            $this->fail('Expected injected failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected failure.', $exception->getMessage());
        }

        $this->assertSame($provider, file_get_contents($this->path.'/app/Providers/RepositoryServiceProvider.php'));
        $this->assertSame($bootstrap, file_get_contents($this->path.'/bootstrap/providers.php'));
        $this->assertFileDoesNotExist($this->path.'/app/Models/Invoice.php');
    }

    public function test_concurrent_provider_creation_is_preserved_and_rolls_back_all_other_writes_even_with_force(): void
    {
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $applicationOwned = "<?php\n// application-owned concurrent provider\n";

        try {
            $plan->commit(true, function ($path) use ($providerPath, $applicationOwned) {
                if ($path === $providerPath) {
                    file_put_contents($path, $applicationOwned);
                }
            });
            $this->fail('Expected concurrent managed-create failure.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('appeared after preflight', $exception->getMessage());
        }

        $this->assertSame($applicationOwned, file_get_contents($providerPath));
        $this->assertFileDoesNotExist($this->path.'/app/Models/Invoice.php');
        $this->assertFileDoesNotExist($this->path.'/app/Contracts/InvoiceRepositoryInterface.php');
        $this->assertStringNotContainsString('RepositoryServiceProvider', file_get_contents($this->path.'/bootstrap/providers.php'));
    }

    public function test_later_conflicting_mapping_is_not_hidden_by_an_earlier_exact_mapping(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\InvoiceRepository::class);
        $this->app->singleton(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\LegacyInvoiceRepository::class);
    }
}
PHP;
        file_put_contents($this->path.'/app/Providers/RepositoryServiceProvider.php', $provider);

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($this->path.'/app/Providers/RepositoryServiceProvider.php', $plan->conflicts(true));
    }

    public function test_mixed_container_receivers_are_all_examined_before_the_verdict(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $provider = <<<'PHP'
<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\InvoiceRepository::class);
        app()->scoped(\App\Contracts\InvoiceRepositoryInterface::class, \App\Repositories\LegacyInvoiceRepository::class);
    }
}
PHP;
        file_put_contents($providerPath, $provider);

        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($providerPath, $plan->conflicts(true));
    }

    public function test_nested_provider_class_literals_do_not_count_as_direct_registration(): void
    {
        file_put_contents($this->path.'/bootstrap/providers.php', <<<'PHP'
<?php
return [
    'metadata' => [App\Providers\RepositoryServiceProvider::class],
    fn () => App\Providers\RepositoryServiceProvider::class,
    App\Providers\AppServiceProvider::class,
];
PHP);

        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $bootstrap = file_get_contents($this->path.'/bootstrap/providers.php');
        token_get_all($bootstrap, TOKEN_PARSE);
        $this->assertSame(3, substr_count($bootstrap, 'App\\Providers\\RepositoryServiceProvider::class'));

        $rerun = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $bootstrapPreview = array_values(array_filter($rerun->preview(), function ($item) {
            return substr($item['path'], -23) === 'bootstrap/providers.php';
        }));
        $this->assertSame('skip', $bootstrapPreview[0]['action']);
    }

    public function test_bootstrap_array_without_trailing_comma_is_updated_with_valid_php(): void
    {
        file_put_contents(
            $this->path.'/bootstrap/providers.php',
            "<?php\n\nreturn [\n    App\\Providers\\AppServiceProvider::class\n];\n"
        );

        $this->factory->make('Invoice', GenerationProfile::REPOSITORY)->commit();
        $bootstrap = file_get_contents($this->path.'/bootstrap/providers.php');
        token_get_all($bootstrap, TOKEN_PARSE);
        $this->assertStringContainsString("App\\Providers\\AppServiceProvider::class,\n", $bootstrap);
        $this->assertStringContainsString("App\\Providers\\RepositoryServiceProvider::class,\n", $bootstrap);
    }

    public function test_three_argument_bind_is_recognized_and_one_argument_mapping_is_ambiguous(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $exact = "<?php\nnamespace App\\Providers;\nuse Illuminate\\Support\\ServiceProvider;\nclass RepositoryServiceProvider extends ServiceProvider { public function register(): void { \$this->app->bind(\\App\\Contracts\\InvoiceRepositoryInterface::class, \\App\\Repositories\\InvoiceRepository::class, true); } }\n";
        file_put_contents($providerPath, $exact);
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $providerPreview = array_values(array_filter($plan->preview(), function ($item) {
            return basename($item['path']) === 'RepositoryServiceProvider.php';
        }));
        $this->assertSame('skip', $providerPreview[0]['action']);

        $ambiguous = "<?php\nnamespace App\\Providers;\nuse Illuminate\\Support\\ServiceProvider;\nclass RepositoryServiceProvider extends ServiceProvider { public function register(): void { \$this->app->bind(\\App\\Contracts\\InvoiceRepositoryInterface::class); } }\n";
        file_put_contents($providerPath, $ambiguous);
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $this->assertContains($providerPath, $plan->conflicts(true));
    }

    public function test_exact_instance_registration_is_equivalent_and_conflicting_instances_are_rejected(): void
    {
        mkdir($this->path.'/app/Providers', 0755, true);
        $providerPath = $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $exact = <<<'PHP'
<?php
namespace App\Providers;
use App\Contracts\InvoiceRepositoryInterface;
use App\Repositories\InvoiceRepository;
use Illuminate\Support\ServiceProvider;
class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->instance(InvoiceRepositoryInterface::class, new InvoiceRepository());
    }
}
PHP;
        file_put_contents($providerPath, $exact);
        $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
        $providerPreview = array_values(array_filter($plan->preview(), function ($item) {
            return basename($item['path']) === 'RepositoryServiceProvider.php';
        }));
        $this->assertSame('skip', $providerPreview[0]['action']);

        foreach ([
            '$this->app->instance(InvoiceRepositoryInterface::class, new LegacyInvoiceRepository());',
            '$this->app->instance(InvoiceRepositoryInterface::class, resolve(InvoiceRepository::class));',
            '$this->app->instance(InvoiceRepositoryInterface::class, $condition ? new InvoiceRepository() : new LegacyInvoiceRepository());',
        ] as $registration) {
            $conflicting = str_replace(
                '$this->app->instance(InvoiceRepositoryInterface::class, new InvoiceRepository());',
                $registration,
                $exact
            );
            file_put_contents($providerPath, $conflicting);
            $plan = $this->factory->make('Invoice', GenerationProfile::REPOSITORY);
            $this->assertContains($providerPath, $plan->conflicts(true), $registration);
        }
    }

    public function test_lean_and_enterprise_do_not_touch_shared_provider_or_bootstrap(): void
    {
        $bootstrap = file_get_contents($this->path.'/bootstrap/providers.php');
        $lean = $this->factory->make('LeanFeature', GenerationProfile::LEAN);
        $enterprise = $this->factory->make('EnterpriseFeature', GenerationProfile::ENTERPRISE, 'app/Domains/Enterprise');

        $this->assertNotContains($this->path.'/app/Providers/RepositoryServiceProvider.php', array_column($lean->preview(), 'path'));
        $this->assertNotContains($this->path.'/bootstrap/providers.php', array_column($enterprise->preview(), 'path'));
        $lean->commit();
        $enterprise->commit();
        $this->assertSame($bootstrap, file_get_contents($this->path.'/bootstrap/providers.php'));
    }

    private function providerWithRegistration($registration): string
    {
        return "<?php\nnamespace App\\Providers;\nuse Illuminate\\Support\\ServiceProvider;\nclass RepositoryServiceProvider extends ServiceProvider\n{\n    public function register(): void\n    {\n        {$registration}\n    }\n}\n";
    }

    private function removeDirectory($directory): void
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
}
