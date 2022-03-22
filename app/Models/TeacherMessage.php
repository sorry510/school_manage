<?php

namespace App\Models;

class TeacherMessage extends Base
{
    protected $table = 'teacher_message';

    public const STATUS_ON = 1;
    public const STATUS_OFF = 2;
    public const STATUS_ALL = 3;

    public const STATUS_TEXTS = [
        self::STATUS_ON => '已接收',
        self::STATUS_OFF => '未接收',
        self::STATUS_ALL => '-',
    ];

    public static function getMessage($params)
    {
        $query = self::select('teacher_message.id', 'teacher_message.content', 'teacher_message.created_at', 'teacher.name as teacher_name', 'student.name as student_name')
            ->leftJoin('teacher', 'teacher.id', '=', 'teacher_message.teacher_id')
            ->leftJoin('student', 'student.id', '=', 'teacher_message.student_id')
            ->orderBy('teacher_message.created_at', 'desc');
        if (is_effective($params, 'teacher_id')) {
            $query->where('teacher_id', $params["teacher_id"]);
        }
        if (is_effective($params, 'student_id')) {
            $query->where('student_id', $params["student_id"]);
        }
        if (is_effective($params, 'search')) {
            $query->where('teacher_message.content', 'like', "%{$params["search"]}%");
        }

        $result = $query->paginate($params["limit"]);
        return $result;
    }
}
