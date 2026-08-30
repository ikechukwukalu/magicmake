<?php

namespace Ikechukwukalu\Magicmake\Response;

use InvalidArgumentException;

class ResponseMode
{
    const VIEW = 'view';
    const JSON = 'json';
    const AUTO = 'auto';

    public static function all()
    {
        return [self::VIEW, self::JSON, self::AUTO];
    }

    public static function normalize($mode)
    {
        $mode = strtolower(trim((string) $mode));
        if (! in_array($mode, self::all(), true)) {
            throw new InvalidArgumentException('Response mode must be one of: view, json, auto.');
        }

        return $mode;
    }
}
