<?php

namespace Ikechukwukalu\Magicmake\Generation;

class ManagedPhpFileResult
{
    /** @var bool */
    private $safe;

    /** @var string */
    private $content;

    private function __construct($safe, $content)
    {
        $this->safe = (bool) $safe;
        $this->content = (string) $content;
    }

    public static function safe($content)
    {
        return new self(true, $content);
    }

    public static function conflict($content)
    {
        return new self(false, $content);
    }

    public function isSafe()
    {
        return $this->safe;
    }

    public function content()
    {
        return $this->content;
    }
}
