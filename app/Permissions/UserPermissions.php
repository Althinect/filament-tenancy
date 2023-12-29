<?php

namespace App\Permissions;

enum UserPermissions: string
{
    use Permissions;

    case ViewAny = 'view-any User';
    case View = 'view User';
    case Create = 'create User';
    case Update = 'update User';
    case Delete = 'delete User';
    case ForceDelete = 'force delete User';
    case Restore = 'restore User';

}
