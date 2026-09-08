<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\GenerationConflictException;
use Ikechukwukalu\Magicmake\Generation\GenerationPlan;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class GenerationPlanTest extends TestCase
{
    /** @var string */
    private $path;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/magicmake-plan-'.uniqid('', true);
        mkdir($this->path, 0755, true);
    }

    protected function tearDown(): void
    {
        $this->removeDirectory($this->path);
    }

    public function test_conflicts_are_reported_before_any_write(): void
    {
        file_put_contents($this->path.'/conflict.php', 'custom');
        $plan = new GenerationPlan($this->path);
        $plan->addFile($this->path.'/created.php', 'created');
        $plan->addFile($this->path.'/conflict.php', 'generated');

        try {
            $plan->commit();
            $this->fail('Expected a generation conflict.');
        } catch (GenerationConflictException $exception) {
            $this->assertSame([$this->path.'/conflict.php'], $exception->conflicts());
        }

        $this->assertFileDoesNotExist($this->path.'/created.php');
        $this->assertSame('custom', file_get_contents($this->path.'/conflict.php'));
    }

    public function test_failed_composite_write_is_rolled_back(): void
    {
        file_put_contents($this->path.'/existing.php', 'custom');
        $plan = new GenerationPlan($this->path);
        $plan->addFile($this->path.'/existing.php', 'generated');
        $plan->addFile($this->path.'/nested/new.php', 'new');

        try {
            $plan->commit(true, function ($path, $index) {
                if ($index === 1) {
                    throw new RuntimeException('Injected failure.');
                }
            });
            $this->fail('Expected the injected failure.');
        } catch (RuntimeException $exception) {
            $this->assertSame('Injected failure.', $exception->getMessage());
        }

        $this->assertSame('custom', file_get_contents($this->path.'/existing.php'));
        $this->assertFileDoesNotExist($this->path.'/nested/new.php');
        $this->assertDirectoryDoesNotExist($this->path.'/nested');
    }

    public function test_unique_append_is_idempotent(): void
    {
        file_put_contents($this->path.'/routes.php', "<?php\n");
        $plan = new GenerationPlan($this->path);
        $plan->addUniqueAppend($this->path.'/routes.php', "Route::get('/health');");
        $plan->commit();
        $plan->commit();

        $this->assertSame(1, substr_count(file_get_contents($this->path.'/routes.php'), "Route::get('/health');"));
    }

    public function test_managed_file_is_revalidated_immediately_before_write(): void
    {
        file_put_contents($this->path.'/managed.php', 'original');
        $plan = new GenerationPlan($this->path);
        $plan->addManagedFile($this->path.'/managed.php', 'original', 'updated');

        $this->expectException(RuntimeException::class);
        try {
            $plan->commit(false, function ($path) {
                file_put_contents($path, 'concurrent change');
            });
        } finally {
            $this->assertSame('concurrent change', file_get_contents($this->path.'/managed.php'));
        }
    }

    public function test_read_dependency_is_revalidated_before_any_write(): void
    {
        file_put_contents($this->path.'/decision.php', 'original');
        $plan = new GenerationPlan($this->path);
        $plan->addReadDependency($this->path.'/decision.php', 'original');
        $plan->addManagedCreate($this->path.'/created.php', 'created');
        file_put_contents($this->path.'/decision.php', 'application change');

        $this->expectException(RuntimeException::class);
        try {
            $plan->commit(true);
        } finally {
            $this->assertSame('application change', file_get_contents($this->path.'/decision.php'));
            $this->assertFileDoesNotExist($this->path.'/created.php');
        }
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
