<?php

namespace App\Permissions;

trait Permissions
{
    public static function all(): array
    {
        return array_map(
            fn (self $permission) => $permission->value,
            self::cases());
    }
}
