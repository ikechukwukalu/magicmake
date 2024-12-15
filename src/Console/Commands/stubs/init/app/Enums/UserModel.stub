<?php

namespace App\Enums;

use App\Models\Admin;
use App\Models\Customer;

enum UserModel: string
{

    case ADMIN = Admin::class;
    case CUSTOMER = Customer::class;

    public static function toArray(): array
    {
        return [
            static::CUSTOMER->value,
            static::ADMIN->value,
        ];

    }

    public static function getType($type): string{
        switch ($type){
            case 'admin' :
                return static::ADMIN->value;
            default:
                return static::CUSTOMER->value;
        }
    }
}
