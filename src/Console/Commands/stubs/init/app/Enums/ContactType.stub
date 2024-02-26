<?php

namespace App\Enums;

enum ContactType: string
{
    case COMPLAINT = 'complaint';
    case INQUIRY = 'inquiry';
    case SUPPORT = 'support';

    public static function toArray(): array
    {
        return [
            static::COMPLAINT->value,
            static::INQUIRY->value,
            static::SUPPORT->value,
        ];
    }
}
