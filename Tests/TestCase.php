<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\MagicmakeServiceProvider;
use Orchestra\Testbench\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
      parent::setUp();
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
    }

    protected function getPackageProviders($app): array
    {
        return [MagicmakeServiceProvider::class];
    }
}
