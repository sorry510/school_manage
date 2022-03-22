<?php

namespace App\Models;

class StudentTeacherMessage extends Base
{
    protected $table = 'student_teacher_message';

    /**
     * @var string 教师to学生
     */
    public const DIRECTION_TEACHER = 1;
    /**
     * @var string 学生to教师
     */
    public const DIRECTION_STUDENT = 2;

    public static function getMessage($params)
    {
        $teacher_id = $params['teacher_id'];
        $student_id = $params['student_id'];
        $last_id = $params['last_id'] ?? 0;
        $query = self::where(function ($query) use ($teacher_id, $student_id) {
            $query->where('teacher_id', $teacher_id)->where('student_id', $student_id);
        })
            ->limit($params['limit'] ?? 30)
            ->orderBy('created_at', 'desc');
        if (is_effective($params, 'search')) {
            $query->where('content', 'like', "%{$params["search"]}%");
        }
        if ($last_id != 0) {
            // 上一次的最早查询id
            $query->where('id', '<', $last_id);
        }
        $result = $query->get();
        return $result;
    }
}
