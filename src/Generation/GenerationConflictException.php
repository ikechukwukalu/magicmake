<?php

namespace Ikechukwukalu\Magicmake\Generation;

use RuntimeException;

class GenerationConflictException extends RuntimeException
{
    /** @var array<int, string> */
    private $conflicts;

    /**
     * @param  array<int, string>  $conflicts
     */
    public function __construct(array $conflicts)
    {
        $this->conflicts = array_values($conflicts);

        parent::__construct("Generation conflicts detected:\n - ".implode("\n - ", $this->conflicts));
    }

    /**
     * @return array<int, string>
     */
    public function conflicts()
    {
        return $this->conflicts;
    }
}
