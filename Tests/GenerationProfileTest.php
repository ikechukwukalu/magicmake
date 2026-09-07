<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Generation\GenerationProfile;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class GenerationProfileTest extends TestCase
{
    public function test_profile_names_are_normalized(): void
    {
        $this->assertSame(GenerationProfile::LEAN, GenerationProfile::normalize('Lean'));
        $this->assertSame(GenerationProfile::REPOSITORY, GenerationProfile::normalize('Repository'));
        $this->assertSame(GenerationProfile::STANDARD, GenerationProfile::normalize('STANDARD'));
        $this->assertSame(GenerationProfile::ENTERPRISE, GenerationProfile::normalize('enterprise'));
    }

    public function test_unknown_profile_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        GenerationProfile::normalize('custom');
    }
}
