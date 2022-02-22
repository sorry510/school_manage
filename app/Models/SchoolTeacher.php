<?php

namespace App\Models;

class SchoolTeacher extends Base
{
    const TYPE_ADMIN = 1;
    const TYPE_NORMAL = 2;

    protected $table = 'school_teacher';

    public static function getAdminInfo($teacher_id, $school_id)
    {
        return self::where('school_id', $school_id)
            ->where('teacher_id', $teacher_id)
            ->where('teacher_type', self::TYPE_ADMIN)
            ->first();
    }
}
