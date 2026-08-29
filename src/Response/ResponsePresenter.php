<?php

namespace Ikechukwukalu\Magicmake\Response;

class ResponsePresenter
{
    /** @var ResponseModeResolver */
    private $resolver;

    public function __construct(?ResponseModeResolver $resolver = null)
    {
        $this->resolver = $resolver ?: new ResponseModeResolver;
    }

    public function present(
        $methodMode,
        $controllerMode,
        $applicationMode,
        $requestWantsJson,
        callable $viewPresenter,
        callable $jsonPresenter
    ) {
        $mode = $this->resolver->resolve(
            $methodMode,
            $controllerMode,
            $applicationMode,
            $requestWantsJson
        );

        return $mode === ResponseMode::JSON ? $jsonPresenter() : $viewPresenter();
    }
}
