<?php

namespace App\Models;

class SchoolTeacher extends Base
{
    public const TYPE_ADMIN = 1;
    public const TYPE_NORMAL = 2;

    public const TYPE_TEXTS = [
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

    public static function getSchoolTeachers($school_ids)
    {
        $query = self::leftJoin('teacher', 'teacher.id', '=', 'school_teacher.teacher_id')
            ->select('teacher.id', 'teacher.name', 'teacher.email', 'school_teacher.school_id', 'school_teacher.teacher_type')
            ->orderBy('school_teacher.teacher_type', 'asc')
            ->orderBy('school_teacher.id', 'asc');
        if (!empty($school_ids)) {
            if (is_array($school_ids)) {
                $query->whereIn('school_id', $school_ids);
            } elseif (is_numeric($school_ids)) {
                $query->where('school_id', $school_ids);
            }
        }
        return $query->get();
    }
}
