<?php

namespace App\Models;

class SchoolTeacher extends Base
{
    const TYPE_ADMIN = 1;
    const TYPE_NORMAL = 2;

    const TYPE_TEXTS = [
        self::TYPE_ADMIN => '管理员',
        self::TYPE_NORMAL => '普通',
    ];

    protected $table = 'school_teacher';

    public static function getAdminInfo($teacher_id, $school_id)
    {
        return self::where('school_id', $school_id)
            ->where('teacher_id', $teacher_id)
            ->where('teacher_type', self::TYPE_ADMIN)
            ->first();
    }

    public static function getSchoolTeachers($school_id)
    {
        return self::where('school_id', $school_id)
            ->leftJoin('teacher', 'teacher.id', '=', 'school_teacher.teacher_id')
            ->select('teacher.id', 'teacher.name', 'teacher.email', 'school_teacher.teacher_type')
            ->orderBy('school_teacher.teacher_type', 'asc')
            ->orderBy('school_teacher.id', 'asc')
            ->get();
    }
}
