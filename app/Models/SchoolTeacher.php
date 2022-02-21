<?php

namespace App\Models;

class SchoolTeacher extends Base
{
    const TYPE_ADMIN = 1;
    const TYPE_NORMAL = 2;

    protected $table = 'school_teacher';
}
