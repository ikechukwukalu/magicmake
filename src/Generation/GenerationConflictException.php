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
    public function __construct(array $conflicts, array $reasons = [])
    {
        $this->conflicts = array_values($conflicts);
        $messages = array_map(function ($path) use ($reasons) {
            return isset($reasons[$path]) ? $path.' — '.$reasons[$path] : $path;
        }, $this->conflicts);

        parent::__construct("Generation conflicts detected:\n - ".implode("\n - ", $messages));
    }

    /**
     * @return array<int, string>
     */
    public function conflicts()
    {
        return $this->conflicts;
    }
}
