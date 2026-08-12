<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Artisan;

class CommandTest extends TestCase
{
    private const MODEL = 'LaravelTwelveProbe';

    private Filesystem $files;

    private string $apiRoutes;

    private bool $apiRouteExisted;

    public function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
        $this->apiRouteExisted = $this->files->exists(base_path('routes/api.php'));
        $this->apiRoutes = $this->apiRouteExisted
            ? $this->files->get(base_path('routes/api.php'))
            : '';
        $this->removeGeneratedArtifacts();
    }

    protected function tearDown(): void
    {
        $this->removeGeneratedArtifacts();

        if ($this->apiRouteExisted) {
            $this->files->put(base_path('routes/api.php'), $this->apiRoutes);
        } else {
            $this->files->delete(base_path('routes/api.php'));
        }

        parent::tearDown();
    }

    public function test_model_command_generates_the_complete_scaffold_on_laravel_12(): void
    {
        $this->artisan('magic:model', ['name' => self::MODEL])
            ->assertSuccessful();

        foreach ($this->expectedArtifacts() as $path) {
            $this->assertFileExists($path);
            $this->assertStringContainsString(self::MODEL, $this->files->get($path));
        }

        $this->assertNotEmpty($this->generatedMigrations());
        $this->assertStringContainsString(
            'LaravelTwelveProbeController::class',
            $this->files->get(base_path('routes/api.php'))
        );
    }

    public function test_package_commands_are_registered(): void
    {
        $commands = Artisan::all();

        foreach ([
            'magic:init',
            'magic:model',
            'magic:contract',
            'magic:repository',
            'magic:service',
            'magic:controller',
            'magic:createRequest',
            'magic:updateRequest',
            'magic:deleteRequest',
            'magic:readRequest',
            'magic:api',
            'magic:test',
            'magic:factory',
        ] as $command) {
            $this->assertArrayHasKey($command, $commands);
        }
    }

    /** @return array<int, string> */
    private function expectedArtifacts(): array
    {
        return [
            app_path('Models/'.self::MODEL.'.php'),
            app_path('Contracts/'.self::MODEL.'RepositoryInterface.php'),
            app_path('Repositories/'.self::MODEL.'Repository.php'),
            app_path('Services/'.self::MODEL.'Service.php'),
            app_path('Http/Controllers/'.self::MODEL.'Controller.php'),
            app_path('Http/Requests/'.self::MODEL.'CreateRequest.php'),
            app_path('Http/Requests/'.self::MODEL.'UpdateRequest.php'),
            app_path('Http/Requests/'.self::MODEL.'DeleteRequest.php'),
            app_path('Http/Requests/'.self::MODEL.'ReadRequest.php'),
            base_path('tests/Feature/'.self::MODEL.'Test.php'),
            database_path('factories/'.self::MODEL.'Factory.php'),
        ];
    }

    /** @return array<int, string> */
    private function generatedMigrations(): array
    {
        return $this->files->glob(database_path('migrations/*_create_laravel_twelve_probes_table.php'));
    }

    private function removeGeneratedArtifacts(): void
    {
        if (! isset($this->files)) {
            return;
        }

        $this->files->delete(array_merge($this->expectedArtifacts(), $this->generatedMigrations()));
    }
}
