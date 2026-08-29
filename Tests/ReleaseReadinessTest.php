<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\FeaturePlanFactory;
use Ikechukwukalu\Magicmake\Generation\GenerationProfile;
use PHPUnit\Framework\TestCase;

class ReleaseReadinessTest extends TestCase
{
    /** @var string */
    private $path;

    /** @var FeaturePlanFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/magicmake-release-'.uniqid('', true);
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

    public function test_every_profile_generates_parseable_output_in_a_clean_application(): void
    {
        $profiles = [
            GenerationProfile::LEAN => 'LeanFeature',
            GenerationProfile::STANDARD => 'StandardFeature',
            GenerationProfile::ENTERPRISE => 'EnterpriseFeature',
        ];

        foreach ($profiles as $profile => $name) {
            $plan = $this->factory->make($name, $profile, 'app/Domains/'.$name);
            $preview = $plan->preview();
            $plan->commit();

            foreach (array_column($preview, 'path') as $path) {
                $this->assertFileExists($path);
                if (substr($path, -4) === '.php') {
                    token_get_all(file_get_contents($path), TOKEN_PARSE);
                    $this->addToAssertionCount(1);
                }
            }
        }
    }

    public function test_package_metadata_declares_the_approved_compatibility_contract(): void
    {
        $composer = json_decode(file_get_contents(dirname(__DIR__).'/composer.json'), true);

        $this->assertSame('^8.2', $composer['require']['php']);
        $this->assertSame('^11.0|^12.0|^13.0', $composer['require']['illuminate/console']);
        $this->assertSame('^11.0|^12.0|^13.0', $composer['require']['illuminate/support']);
        $this->assertSame('^9.0|^10.0|^11.0', $composer['require-dev']['orchestra/testbench']);
    }

    public function test_standard_plan_generation_stays_within_the_release_budget(): void
    {
        $startedAt = microtime(true);

        for ($index = 0; $index < 100; $index++) {
            $preview = $this->factory->make('PerformanceFeature'.$index)->preview();
            $this->assertCount(13, $preview);
        }

        $this->assertLessThan(
            2.0,
            microtime(true) - $startedAt,
            'Planning 100 Standard features exceeded the two-second release budget.'
        );
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
}
