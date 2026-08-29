<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Response\ResponseMode;
use Ikechukwukalu\Magicmake\Response\ResponseModeResolver;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ResponseModeResolverTest extends TestCase
{
    public function test_every_precedence_and_negotiation_combination(): void
    {
        $resolver = new ResponseModeResolver;
        $optionalModes = [null, ResponseMode::VIEW, ResponseMode::JSON, ResponseMode::AUTO];

        foreach ($optionalModes as $methodMode) {
            foreach ($optionalModes as $controllerMode) {
                foreach (ResponseMode::all() as $applicationMode) {
                    foreach ([false, true] as $requestWantsJson) {
                        $active = $methodMode ?: ($controllerMode ?: $applicationMode);
                        $expected = $active === ResponseMode::AUTO
                            ? ($requestWantsJson ? ResponseMode::JSON : ResponseMode::VIEW)
                            : $active;

                        $this->assertSame(
                            $expected,
                            $resolver->resolve($methodMode, $controllerMode, $applicationMode, $requestWantsJson)
                        );
                    }
                }
            }
        }
    }

    public function test_invalid_active_mode_is_rejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        (new ResponseModeResolver)->resolve('xml', null, ResponseMode::AUTO, false);
    }
}
