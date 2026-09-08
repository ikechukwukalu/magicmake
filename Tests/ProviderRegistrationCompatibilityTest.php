<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\ApplicationBootstrapInspector;
use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Ikechukwukalu\Magicmake\Generation\GenerationConflictException;
use Ikechukwukalu\Magicmake\Generation\GenerationProfile;
use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class ProviderRegistrationCompatibilityTest extends TestCase
{
    private $path;
    private $factory;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/magicmake-registration-'.uniqid('', true);
        mkdir($this->path.'/routes', 0755, true);
        mkdir($this->path.'/bootstrap', 0755, true);
        mkdir($this->path.'/config', 0755, true);
        file_put_contents($this->path.'/routes/api.php', "<?php\n");
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

    public function test_modern_application_creates_missing_bootstrap_provider_file(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->modernBootstrap());

        $this->factory->make('ModernInvoice', GenerationProfile::REPOSITORY)->commit();

        $this->assertSame(
            "<?php\n\nreturn [\n    App\\Providers\\RepositoryServiceProvider::class,\n];\n",
            file_get_contents($this->path.'/bootstrap/providers.php')
        );
        $this->assertFileExists($this->path.'/app/Models/ModernInvoice.php');
    }

    public function test_modern_provider_creation_is_dry_run_safe_and_rolls_back(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->modernBootstrap());
        $plan = $this->factory->make('ModernRollback', GenerationProfile::REPOSITORY);
        $afterRegistration = $this->path.'/after-registration.php';
        $plan->addManagedCreate($afterRegistration, "<?php\n");
        $preview = $plan->preview();
        $this->assertContains($this->path.'/bootstrap/providers.php', array_column($preview, 'path'));
        $this->assertFileDoesNotExist($this->path.'/bootstrap/providers.php');

        try {
            $plan->commit(false, function ($path) use ($afterRegistration) {
                if ($path === $afterRegistration) {
                    throw new RuntimeException('Injected modern registration failure.');
                }
            });
            $this->fail('Expected injected failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected modern registration failure.', $exception->getMessage());
        }

        $this->assertFileDoesNotExist($this->path.'/bootstrap/providers.php');
        $this->assertFileDoesNotExist($afterRegistration);
        $this->assertFileDoesNotExist($this->path.'/app/Models/ModernRollback.php');
        $this->assertFileDoesNotExist($this->path.'/app/Providers/RepositoryServiceProvider.php');
    }

    public function test_concurrent_modern_provider_file_is_preserved_and_rolls_back(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->modernBootstrap());
        $plan = $this->factory->make('ConcurrentModern', GenerationProfile::REPOSITORY);
        $applicationOwned = "<?php\nreturn []; // appeared concurrently\n";

        try {
            $plan->commit(false, function ($path) use ($applicationOwned) {
                if ($path === $this->path.'/bootstrap/providers.php') {
                    file_put_contents($path, $applicationOwned);
                }
            });
            $this->fail('Expected concurrent creation failure.');
        } catch (RuntimeException $exception) {
            $this->assertStringContainsString('appeared after preflight', $exception->getMessage());
        }

        $this->assertSame($applicationOwned, file_get_contents($this->path.'/bootstrap/providers.php'));
        $this->assertFileDoesNotExist($this->path.'/app/Models/ConcurrentModern.php');
    }

    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function test_legacy_laravel_twelve_existing_registration_is_unchanged_and_resolves(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $config = $this->legacyConfig('        App\\Providers\\RepositoryServiceProvider::class,');
        file_put_contents($this->path.'/config/app.php', $config);

        $this->factory->make('UserExportInvoice', GenerationProfile::REPOSITORY)->commit();

        $this->assertSame($config, file_get_contents($this->path.'/config/app.php'));
        $this->assertFileDoesNotExist($this->path.'/bootstrap/providers.php');
        $this->assertFileExists($this->path.'/app/Services/UserExportInvoiceService.php');
        require_once $this->path.'/app/Models/UserExportInvoice.php';
        require_once $this->path.'/app/Contracts/UserExportInvoiceRepositoryInterface.php';
        require_once $this->path.'/app/Repositories/UserExportInvoiceRepository.php';
        require_once $this->path.'/app/Providers/RepositoryServiceProvider.php';
        $container = new \Illuminate\Container\Container();
        $provider = new \App\Providers\RepositoryServiceProvider($container);
        $provider->register();
        $this->assertInstanceOf(
            \App\Repositories\UserExportInvoiceRepository::class,
            $container->make(\App\Contracts\UserExportInvoiceRepositoryInterface::class)
        );
    }

    public function test_legacy_missing_registration_is_inserted_once_and_rerun_is_idempotent(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $original = $this->legacyConfig('        // Preserve this application comment.');
        file_put_contents($this->path.'/config/app.php', $original);
        $plan = $this->factory->make('LegacyInvoice', GenerationProfile::REPOSITORY);

        $this->assertSame('update', $this->actionFor($plan->preview(), $this->path.'/config/app.php'));
        $this->assertSame($original, file_get_contents($this->path.'/config/app.php'));
        $plan->commit();
        $updated = file_get_contents($this->path.'/config/app.php');
        $this->assertStringContainsString('// Preserve this application comment.', $updated);
        $this->assertSame(1, substr_count($updated, 'App\\Providers\\RepositoryServiceProvider::class'));

        $this->factory->make('LegacyInvoice', GenerationProfile::REPOSITORY)->commit();
        $this->assertSame($updated, file_get_contents($this->path.'/config/app.php'));
    }

    public function test_legacy_bootstrap_uses_config_registry_and_preserves_stray_bootstrap_provider_file(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $config = $this->legacyConfig('        App\\Providers\\RepositoryServiceProvider::class,');
        $stray = "<?php\n\n// This file is not loaded by the legacy application bootstrap.\nreturn [];\n";
        file_put_contents($this->path.'/config/app.php', $config);
        file_put_contents($this->path.'/bootstrap/providers.php', $stray);

        $this->factory->make('LegacyStrayRegistry', GenerationProfile::REPOSITORY)->commit();

        $this->assertSame($config, file_get_contents($this->path.'/config/app.php'));
        $this->assertSame($stray, file_get_contents($this->path.'/bootstrap/providers.php'));
        $this->assertFileExists($this->path.'/app/Models/LegacyStrayRegistry.php');
    }

    public function test_legacy_registration_update_is_rolled_back_byte_for_byte(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $original = $this->legacyConfig('        App\\Providers\\AppAnalyticsServiceProvider::class,');
        file_put_contents($this->path.'/config/app.php', $original);
        $plan = $this->factory->make('LegacyRollback', GenerationProfile::REPOSITORY);
        $afterRegistration = $this->path.'/after-registration.php';
        $plan->addManagedCreate($afterRegistration, "<?php\n");

        try {
            $plan->commit(false, function ($path) use ($afterRegistration) {
                if ($path === $afterRegistration) {
                    throw new RuntimeException('Injected legacy registration failure.');
                }
            });
            $this->fail('Expected injected failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected legacy registration failure.', $exception->getMessage());
        }

        $this->assertSame($original, file_get_contents($this->path.'/config/app.php'));
        $this->assertFileDoesNotExist($this->path.'/app/Models/LegacyRollback.php');
        $this->assertFileDoesNotExist($this->path.'/app/Providers/RepositoryServiceProvider.php');
    }

    public function test_legacy_exact_registration_import_alias_comment_and_comma_variants_are_preserved(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $configs = [
            $this->legacyConfig('        \\App\\Providers\\RepositoryServiceProvider::class'),
            str_replace(
                "use Illuminate\\Support\\ServiceProvider;",
                "use Illuminate\\Support\\ServiceProvider;\nuse App\\Providers\\RepositoryServiceProvider;",
                $this->legacyConfig('        RepositoryServiceProvider::class, // keep registration')
            ),
            str_replace(
                "use Illuminate\\Support\\ServiceProvider;",
                "use Illuminate\\Support\\ServiceProvider;\nuse App\\Providers\\RepositoryServiceProvider as Repositories;",
                $this->legacyConfig('        Repositories::class,')
            ),
        ];

        foreach ($configs as $index => $config) {
            file_put_contents($this->path.'/config/app.php', $config);
            $plan = $this->factory->make('AliasInvoice'.$index, GenerationProfile::REPOSITORY);
            $this->assertNotContains($this->path.'/config/app.php', $plan->conflicts(true));
            $this->assertSame('skip', $this->actionFor($plan->preview(), $this->path.'/config/app.php'));
            $this->assertSame($config, file_get_contents($this->path.'/config/app.php'));
        }
    }

    public function test_legacy_first_segment_namespace_alias_and_case_insensitive_names_do_not_duplicate_registration(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $config = str_replace(
            "use Illuminate\\Support\\ServiceProvider;",
            "use illuminate\\support\\serviceprovider;\nuse App\\Providers as Providers;",
            $this->legacyConfig('        providers\\repositoryserviceprovider::class,')
        );
        file_put_contents($this->path.'/config/app.php', $config);

        $plan = $this->factory->make('NamespaceAliasInvoice', GenerationProfile::REPOSITORY);

        $this->assertNotContains($this->path.'/config/app.php', $plan->conflicts(true));
        $this->assertSame('skip', $this->actionFor($plan->preview(), $this->path.'/config/app.php'));
        $plan->commit();
        $this->assertSame($config, file_get_contents($this->path.'/config/app.php'));
    }

    public function test_legacy_unresolved_target_basename_is_blocked(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        file_put_contents(
            $this->path.'/config/app.php',
            $this->legacyConfig('        Unknown\\RepositoryServiceProvider::class,')
        );

        $plan = $this->factory->make('UnresolvedAliasInvoice', GenerationProfile::REPOSITORY);

        $this->assertContains($this->path.'/config/app.php', $plan->conflicts(true));
        $this->assertFileDoesNotExist($this->path.'/app/Models/UnresolvedAliasInvoice.php');
    }

    public function test_modern_bootstrap_requires_canonical_top_level_returned_configure_chain(): void
    {
        $invalidBootstraps = [
            "<?php\nuse Illuminate\\Foundation\\Application;\nApplication::configure(basePath: __DIR__);\nreturn new stdClass();\n",
            "<?php\nuse Illuminate\\Foundation\\Application;\n\$configured = Application::configure(basePath: __DIR__)->create();\nreturn \$configured;\n",
            "<?php\nuse Illuminate\\Foundation\\Application;\nif (false) { return Application::configure(basePath: __DIR__)->create(); }\nreturn new stdClass();\n",
            "<?php\nuse Illuminate\\Foundation\\Application;\nreturn Application::configure(basePath: __DIR__)->withRouting();\n",
        ];

        foreach ($invalidBootstraps as $index => $bootstrap) {
            file_put_contents($this->path.'/bootstrap/app.php', $bootstrap);
            $plan = $this->factory->make('IncidentalModern'.$index, GenerationProfile::REPOSITORY);
            $this->assertContains($this->path.'/bootstrap/app.php', $plan->conflicts(true), (string) $index);
            $this->assertFileDoesNotExist($this->path.'/bootstrap/providers.php');
        }
    }

    public function test_bootstrap_candidates_under_unbraced_or_alternative_control_flow_are_rejected(): void
    {
        $bootstraps = [
            "<?php\nuse Illuminate\\Foundation\\Application;\nif (\$enabled) return Application::configure(basePath: __DIR__)->create();\n",
            "<?php\nuse Illuminate\\Foundation\\Application;\nforeach ([1] as \$value) return Application::configure(basePath: __DIR__)->create();\n",
            "<?php\nuse Illuminate\\Foundation\\Application;\nwhile (\$enabled):\nreturn Application::configure(basePath: __DIR__)->create();\nendwhile;\n",
            "<?php\nuse Illuminate\\Foundation\\Application;\nswitch (\$value):\ncase 1:\nreturn Application::configure(basePath: __DIR__)->create();\nendswitch;\n",
            "<?php\nif (\$enabled) \$ignored = true; else \$app = new Illuminate\\Foundation\\Application(__DIR__);\nreturn \$app;\n",
            "<?php\nif (\$enabled):\n\$app = new Illuminate\\Foundation\\Application(__DIR__);\nendif;\nreturn \$app;\n",
            "<?php\nforeach ([1] as \$value):\n\$app = new Illuminate\\Foundation\\Application(__DIR__);\nendforeach;\nreturn \$app;\n",
            "<?php\nwhile (\$enabled) \$app = new Illuminate\\Foundation\\Application(__DIR__);\nreturn \$app;\n",
            "<?php\nswitch (\$value):\ncase 1:\n\$app = new Illuminate\\Foundation\\Application(__DIR__);\nendswitch;\nreturn \$app;\n",
        ];

        $inspector = new ApplicationBootstrapInspector();
        foreach ($bootstraps as $index => $bootstrap) {
            $this->assertNull($inspector->mode($bootstrap), (string) $index);
        }
    }

    public function test_legacy_insertion_preserves_crlf_and_existing_provider_indentation(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $config = "<?php\r\n\r\nuse Illuminate\\Support\\ServiceProvider;\r\n\r\nreturn [\r\n  'providers' => ServiceProvider::defaultProviders()->merge([\r\n      App\\Providers\\AppServiceProvider::class\r\n  ])->toArray(),\r\n];\r\n";
        file_put_contents($this->path.'/config/app.php', $config);

        $this->factory->make('FormattedLegacyInvoice', GenerationProfile::REPOSITORY)->commit();
        $updated = file_get_contents($this->path.'/config/app.php');

        $this->assertStringNotContainsString("\n", str_replace("\r\n", '', $updated));
        $this->assertStringContainsString(
            "      App\\Providers\\AppServiceProvider::class,\r\n      App\\Providers\\RepositoryServiceProvider::class,\r\n  ])->toArray()",
            $updated
        );
    }

    public function test_legacy_registration_source_is_revalidated_before_any_write(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $config = $this->legacyConfig('        App\\Providers\\RepositoryServiceProvider::class,');
        file_put_contents($this->path.'/config/app.php', $config);
        $plan = $this->factory->make('ChangedLegacy', GenerationProfile::REPOSITORY);
        file_put_contents($this->path.'/config/app.php', $config."\n// changed after planning\n");

        try {
            $plan->commit();
            $this->fail('Expected changed registration source conflict.');
        } catch (GenerationConflictException $exception) {
            $this->assertContains($this->path.'/config/app.php', $exception->conflicts());
        } finally {
            $this->assertFileDoesNotExist($this->path.'/app/Models/ChangedLegacy.php');
        }
    }

    public function test_legacy_ambiguous_dynamic_duplicate_and_malformed_lists_are_blocked_even_with_force(): void
    {
        file_put_contents($this->path.'/bootstrap/app.php', $this->legacyBootstrap());
        $entries = [
            'duplicate' => "        App\\Providers\\RepositoryServiceProvider::class,\n        App\\Providers\\RepositoryServiceProvider::class,",
            'dynamic' => '        $enabled ? App\\Providers\\RepositoryServiceProvider::class : App\\Providers\\AppServiceProvider::class,',
            'unrelated_dynamic' => '        ...$applicationProviders,',
            'ambiguous' => '        RepositoryServiceProvider::class,',
        ];

        foreach ($entries as $name => $entry) {
            file_put_contents($this->path.'/config/app.php', $this->legacyConfig($entry));
            $model = 'Blocked'.str_replace('_', '', ucwords($name, '_'));
            $plan = $this->factory->make($model, GenerationProfile::REPOSITORY);
            $this->assertContains($this->path.'/config/app.php', $plan->conflicts(true), $name);
            try {
                $plan->commit(true);
                $this->fail('Expected conflict for '.$name);
            } catch (GenerationConflictException $exception) {
                $this->assertContains($this->path.'/config/app.php', $exception->conflicts());
            }
            $this->assertFileDoesNotExist($this->path.'/app/Models/'.$model.'.php');
        }

        file_put_contents($this->path.'/config/app.php', "<?php\nreturn [;\n");
        $plan = $this->factory->make('BlockedMalformed', GenerationProfile::REPOSITORY);
        $this->assertContains($this->path.'/config/app.php', $plan->conflicts(true));
    }

    private function actionFor(array $preview, $path)
    {
        foreach ($preview as $item) {
            if ($item['path'] === $path) return $item['action'];
        }

        return null;
    }

    private function modernBootstrap(): string
    {
        return <<<'PHP'
<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(web: __DIR__.'/../routes/web.php')
    ->create();
PHP;
    }

    private function legacyBootstrap(): string
    {
        return <<<'PHP'
<?php

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);
$app->singleton(Illuminate\Contracts\Http\Kernel::class, App\Http\Kernel::class);
$app->singleton(Illuminate\Contracts\Console\Kernel::class, App\Console\Kernel::class);

return $app;
PHP;
    }

    private function legacyConfig($entry): string
    {
        return "<?php\n\nuse Illuminate\\Support\\ServiceProvider;\n\nreturn [\n    'name' => 'Compatibility application',\n    'providers' => ServiceProvider::defaultProviders()->merge([\n        App\\Providers\\AppServiceProvider::class,\n{$entry}\n    ])->toArray(),\n];\n";
    }

    private function removeDirectory($directory): void
    {
        if (! is_dir($directory)) return;
        foreach (array_diff(scandir($directory), ['.', '..']) as $item) {
            $path = $directory.'/'.$item;
            is_dir($path) ? $this->removeDirectory($path) : unlink($path);
        }
        rmdir($directory);
    }
}
