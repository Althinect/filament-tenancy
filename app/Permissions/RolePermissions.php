<?php

namespace App\Permissions;

enum RolePermissions: string
{
    use Permissions;

    case ViewAny = 'view-any Role';
    case View = 'view Role';
    case Create = 'create Role';
    case Update = 'update Role';
    case Delete = 'delete Role';
    case ForceDelete = 'force delete Role';
    case Restore = 'restore Role';

}
