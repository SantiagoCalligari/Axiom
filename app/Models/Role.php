<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    public const ADMIN = 'admin';

    public const TEACHER = 'teacher';

    public const USER = 'user';

    public const UNIVERSITY_ADMIN = 'university_admin';

    public const CAREER_ADMIN = 'career_admin';

    public const SUBJECT_ADMIN = 'subject_admin';
}
