<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public const ADMIN = 'admin';

    public const TEACHER = 'teacher';

    public const USER = 'user';
}
