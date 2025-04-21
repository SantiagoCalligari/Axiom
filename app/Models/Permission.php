<?php

namespace App\Models;

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class Permission extends SpatiePermission
{
    public const STORE_UNIVERSITY = 'store university';
    public const DELETE_UNIVERSITY = 'delete university';
    public const STORE_CAREER = 'store career';
    public const DELETE_CAREER = 'delete career';
}
