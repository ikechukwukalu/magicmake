<?php

namespace Ikechukwukalu\Magicmake\Response;

class ResponseModeResolver
{
    public function resolve($methodMode, $controllerMode, $applicationMode, $requestWantsJson)
    {
        $activeMode = $methodMode;
        if ($activeMode === null || trim((string) $activeMode) === '') {
            $activeMode = $controllerMode;
        }
        if ($activeMode === null || trim((string) $activeMode) === '') {
            $activeMode = $applicationMode;
        }

        $activeMode = ResponseMode::normalize($activeMode);
        if ($activeMode === ResponseMode::AUTO) {
            return $requestWantsJson ? ResponseMode::JSON : ResponseMode::VIEW;
        }

        return $activeMode;
    }
}
