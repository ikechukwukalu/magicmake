<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\Psr4NamespaceResolver;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class Psr4NamespaceResolverTest extends TestCase
{
    /** @var string */
    private $path;

    protected function setUp(): void
    {
        $this->path = sys_get_temp_dir().'/magicmake-psr4-'.uniqid('', true);
        mkdir($this->path, 0755, true);
        file_put_contents($this->path.'/composer.json', json_encode([
            'autoload' => ['psr-4' => ['App\\' => 'app/', 'Modules\\' => ['modules/', 'packages/modules/']]],
        ]));
    }

    protected function tearDown(): void
    {
        unlink($this->path.'/composer.json');
        rmdir($this->path);
    }

    public function test_path_only_derives_namespace(): void
    {
        $target = (new Psr4NamespaceResolver($this->path))->resolve('app/Domains/Billing');
        $this->assertSame('App\\Domains\\Billing', $target->namespaceName());
    }

    public function test_namespace_only_requires_path_when_mapping_is_ambiguous(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Psr4NamespaceResolver($this->path))->resolve(null, 'Modules\\Billing');
    }

    public function test_unsafe_target_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Psr4NamespaceResolver($this->path))->resolve('app/../outside');
    }

    public function test_absolute_target_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new Psr4NamespaceResolver($this->path))->resolve('/app/Domains/Billing');
    }
}
