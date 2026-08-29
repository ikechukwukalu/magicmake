<?php

namespace Ikechukwukalu\Magicmake\Generation;

class GenerationTarget
{
    /** @var string|null */
    private $path;

    /** @var string|null */
    private $namespace;

    public function __construct($path, $namespace)
    {
        $this->path = $path;
        $this->namespace = $namespace;
    }

    public function isModular()
    {
        return $this->path !== null;
    }

    public function path()
    {
        return $this->path;
    }

    public function namespaceName()
    {
        return $this->namespace;
    }
}
