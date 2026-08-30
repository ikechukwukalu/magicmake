<?php

namespace Ikechukwukalu\Magicmake\Tests;

use Ikechukwukalu\Magicmake\Response\ResponseMode;
use Ikechukwukalu\Magicmake\Response\ResponsePresenter;
use PHPUnit\Framework\TestCase;

class ResponsePresenterTest extends TestCase
{
    public function test_every_resolved_mode_invokes_only_its_presenter(): void
    {
        $presenter = new ResponsePresenter;

        foreach ([ResponseMode::VIEW, ResponseMode::JSON, ResponseMode::AUTO] as $mode) {
            foreach ([false, true] as $requestWantsJson) {
                $viewCalls = 0;
                $jsonCalls = 0;
                $result = $presenter->present(
                    $mode,
                    null,
                    ResponseMode::AUTO,
                    $requestWantsJson,
                    function () use (&$viewCalls) {
                        $viewCalls++;
                        return 'view response';
                    },
                    function () use (&$jsonCalls) {
                        $jsonCalls++;
                        return 'json response';
                    }
                );

                $expectsJson = $mode === ResponseMode::JSON || ($mode === ResponseMode::AUTO && $requestWantsJson);
                $this->assertSame($expectsJson ? 'json response' : 'view response', $result);
                $this->assertSame($expectsJson ? 0 : 1, $viewCalls);
                $this->assertSame($expectsJson ? 1 : 0, $jsonCalls);
            }
        }
    }
}
