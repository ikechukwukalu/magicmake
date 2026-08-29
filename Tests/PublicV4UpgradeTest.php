<?php

namespace Ikechukwukalu\Magicmake\Tests;

use PHPUnit\Framework\TestCase;

class PublicV4UpgradeTest extends TestCase
{
    public function testPublicV4RuntimeDependenciesHaveAnExplicitV5Transition(): void
    {
        $current = $this->readJson(dirname(__DIR__).'/composer.json');
        $publicV4 = $this->readJson(__DIR__.'/Fixtures/public-v4.0.0-require.json');

        $this->assertSame('ikechukwukalu/magicmake', $current['name']);
        $this->assertSame('ikechukwukalu/magicmake', $publicV4['name']);
        $this->assertSame('c090da47e9af88b2951f670d115b1f5de9a02f61', $publicV4['source_commit']);
        $this->assertSame([
            'illuminate/console',
            'illuminate/support',
            'php',
            'symfony/console',
            'symfony/finder',
        ], array_keys($current['require']));

        $core = array_fill_keys(array_keys($current['require']), true);
        $formerRuntimeIntegrations = array_diff_key($publicV4['require'], $core);

        foreach ($formerRuntimeIntegrations as $package => $constraint) {
            $this->assertArrayNotHasKey($package, $current['require']);

            if ($package === 'ikechukwukalu/requirepin') {
                $this->assertArrayNotHasKey($package, $current['suggest']);
                continue;
            }

            $this->assertArrayHasKey(
                $package,
                $current['suggest'],
                sprintf('Former v4 runtime dependency [%s] must remain documented as a v5 suggestion.', $package)
            );
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function readJson(string $path): array
    {
        $contents = file_get_contents($path);
        $this->assertNotFalse($contents);

        return json_decode($contents, true, flags: JSON_THROW_ON_ERROR);
    }
}
