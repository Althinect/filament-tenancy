<?php

namespace App\Permissions;

enum PermissionPermissions: string
{
    use Permissions;

    case ViewAny = 'view-any Permission';
    case View = 'view Permission';
    case Create = 'create Permission';
    case Update = 'update Permission';
    case Delete = 'delete Permission';
    case ForceDelete = 'force delete Permission';
    case Restore = 'restore Permission';
}
