<?php

namespace App\Models;

class AdminMessage extends Base
{
    protected $table = 'admin_message';

    public const TYPE_ALL = 1;
    public const TYPE_TEACHER = 2;
    public const TYPE_STUDENT = 3;
    public const TYPE_LINE = 4;

    public const TYPE_TEXTS = [
        self::TYPE_ALL => '广播',
        self::TYPE_TEACHER => '教师',
        self::TYPE_STUDENT => '学生',
        self::TYPE_LINE => 'line用户',
    ];

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
        $query = self::select('id', 'content', 'created_at')
            ->orderBy('created_at', 'desc')
            ->where(function ($query) use ($params) {
                if (isset($params['teacher_id'])) {
                    $query->orWhere('teacher_id', $params["teacher_id"]); // 定向
                }
                if (isset($params['student_id'])) {
                    $query->orWhere('student_id', $params["student_id"]); // 定向
                }
                $query->orWhere('type', self::TYPE_ALL); // 广播
            });
        if (is_effective($params, 'search')) {
            $query->where('content', 'like', "%{$params["search"]}%");
        }

        $result = $query->paginate($params["limit"]);
        return $result;
    }
}
