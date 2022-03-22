<?php

namespace App\Http\Controllers\Student;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\AdminMessage;
use App\Models\LineUserRelation;
use App\Models\School;
use App\Models\SchoolTeacher;
use App\Models\StudentTeacherLike;
use App\Models\StudentTeacherMessage;
use App\Models\Teacher;
use App\Models\TeacherMessage;
use Illuminate\Http\Request;
use Sorry510\Annotations\RequestParam;

class StudentController extends Controller
{
    /**
     * @OA\post(
     *     tags={"学生"},
     *     path="/api/student/like",
     *     summary="关注教师",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="teacher_id", type="integer", description="教师id"),
     *                 required={"teacher_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function like(Request $request)
    {
        $params = $request->only('teacher_id');
        $user = $request->user();
        try {
            if (StudentTeacherLike::where('teacher_id', $params['teacher_id'])->where('student_id', $user->id)->first()) {
                return $this->resJson(ErrorCode::REPEAT_OPERATION);
            }
            StudentTeacherLike::create([
                'teacher_id' => $params['teacher_id'],
                'student_id' => $user->id,
            ]);
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     tags={"学生"},
     *     path="/api/student/unlike",
     *     summary="取消关注教师",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="teacher_id", type="integer", description="教师id"),
     *                 required={"teacher_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function unlike(Request $request)
    {
        $params = $request->only('teacher_id');
        $user = $request->user();
        try {
            if (!StudentTeacherLike::where('teacher_id', $params['teacher_id'])->where('student_id', $user->id)->first()) {
                return $this->resJson(ErrorCode::REPEAT_OPERATION);
            }
            StudentTeacherLike::where([
                'teacher_id' => $params['teacher_id'],
                'student_id' => $user->id,
            ])->delete();
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"学生"},
     *     path="/api/student/student-info",
     *     summary="学生信息",
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="id", type="string", description="id"),
     *             @OA\Property(property="account", type="string", description="用户"),
     *             @OA\Property(property="name", type="string", description="姓名"),
     *             @OA\Property(property="school", type="string", description="学校"),
     *             @OA\Property(property="school_remark", type="string", description="学校备注"),
     *             @OA\Property(property="hasBindLine", type="bool", description="是否已绑定line"),
     *             @OA\Property(property="created_at", type="string", description="创建时间"),
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     */
    public function getStudentInfo(Request $request)
    {
        $user = $request->user();
        $school = School::select('id', 'name', 'remark')->where('id', $user->school_id)->first();
        $user->school = $school->name;
        $user->school_remark = $school->remark;
        $user->hasBindLine = !!LineUserRelation::where('relation_id', $user->id)
            ->where('type', LineUserRelation::TYPE_STUDENT)
            ->first();
        return $this->resJson(ErrorCode::SUCCESS, $user);
    }

    /**
     * @OA\get(
     *     tags={"学生"},
     *     path="/api/student/teachers",
     *     summary="教师列表",
     *     @OA\Parameter(name="search", in="query", description="搜索"),
     *     @OA\Parameter(name="page", in="query", description="页数"),
     *     @OA\Parameter(name="limit", in="query", description="每页数量"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="meta", type="object", description="元信息",
     *                 @OA\Property(property="count", type="integer", description="当前页的项目数"),
     *                 @OA\Property(property="perPage", type="integer", description="每页显示的项目数"),
     *                 @OA\Property(property="currentPage", type="integer", description="当前页码"),
     *                 @OA\Property(property="lastPage", type="integer", description="最后一页的页码"),
     *                 @OA\Property(property="total", type="integer", description="总数")
     *             ),
     *             @OA\Property(property="list", type="array", description="数据列表", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", description="唯一标识"),
     *                 @OA\Property(property="name", type="string", description="姓名"),
     *                 @OA\Property(property="email", type="string", description="邮箱"),
     *                 @OA\Property(property="online", type="integer", description="是否在线[1:是,2:否]"),
     *                 @OA\Property(property="isLike", type="integer", description="是否关注[1:是,2:否]"),
     *                 @OA\Property(property="created_at", type="string", description="创建时间")
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     *
     * @RequestParam(fields={"page": 1, "limit": 10 })
     */
    public function getTeacherList(Request $request)
    {
        $params = $request->only("search", "limit");
        $user = $request->user();
        $teachersIds = SchoolTeacher::where('school_id', $user->school_id)->pluck('teacher_id');
        $query = Teacher::whereIn('id', $teachersIds)
            ->select('id', 'name', 'email', 'online')
            ->where('status', Teacher::STATUS_ACTIVE)
            ->orderBy('id', 'desc');
        if (is_effective($params, 'search')) {
            $query->where(function ($query) use ($params) {
                $query->where('name', 'like', "%{$params["search"]}%")
                    ->orWhere('email', 'like', "%{$params["search"]}%");
            });
        }
        $teachers = $query->paginate($params['limit']);
        $mapLikeIds = StudentTeacherLike::where('student_id', $user->id)->pluck('id', 'teacher_id');
        $teachers->each(function ($teacher) use ($mapLikeIds) {
            $teacher->isLike = isset($mapLikeIds[$teacher->id]) ? 1 : 2;
        });
        return $this->resJson(ErrorCode::SUCCESS, $teachers);
    }

    /**
     * @OA\get(
     *     tags={"学生"},
     *     path="/api/student/chat-messages",
     *     summary="聊天记录",
     *     @OA\Parameter(name="receiver_id", in="query", description="接收人id"),
     *     @OA\Parameter(name="search", in="query", description="搜索"),
     *     @OA\Parameter(name="last_id", in="query", description="最早id"),
     *     @OA\Parameter(name="limit", in="query", description="每页数量"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="array", description="数据列表", @OA\Items(type="object",
     *            @OA\Property(property="id", type="integer", description="唯一标识"),
     *            @OA\Property(property="teacher_id", type="integer", description="教师id"),
     *            @OA\Property(property="student_id", type="integer", description="学生id"),
     *            @OA\Property(property="direction", type="integer", description="交流方向[1:教师to学生,2:学生to教师]"),
     *            @OA\Property(property="content", type="string", description="内容"),
     *            @OA\Property(property="status", type="integer", description="接收状态[1:已接收,2:未接收]"),
     *            @OA\Property(property="created_at", type="string", description="创建时间")
     *         )),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     *
     * @RequestParam(fields={"last_id": 0, "limit": 30 })
     */
    public function getMessageList(Request $request)
    {
        $params = $request->only("receiver_id", "search", "last_id", "limit");
        $user = $request->user();
        $params['teacher_id'] = $request['receiver_id'];
        $params['student_id'] = $user->id;
        $result = StudentTeacherMessage::getMessage($params);
        return $this->resJson(ErrorCode::SUCCESS, $result);
    }

    /**
     * @OA\get(
     *     tags={"学生"},
     *     path="/api/student/admin-messages",
     *     summary="管理员推送消息",
     *     @OA\Parameter(name="search", in="query", description="搜索"),
     *     @OA\Parameter(name="page", in="query", description="每页数量"),
     *     @OA\Parameter(name="limit", in="query", description="每页数量"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="meta", type="object", description="元信息",
     *                 @OA\Property(property="count", type="integer", description="当前页的项目数"),
     *                 @OA\Property(property="perPage", type="integer", description="每页显示的项目数"),
     *                 @OA\Property(property="currentPage", type="integer", description="当前页码"),
     *                 @OA\Property(property="lastPage", type="integer", description="最后一页的页码"),
     *                 @OA\Property(property="total", type="integer", description="总数")
     *             ),
     *             @OA\Property(property="list", type="array", description="数据列表", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", description="唯一标识"),
     *                 @OA\Property(property="content", type="string", description="内容"),
     *                 @OA\Property(property="created_at", type="string", description="创建时间")
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     *
     * @RequestParam(fields={"page": 1, "limit": 30 })
     */
    public function getAdminMessageList(Request $request)
    {
        $params = $request->only("search", "page", "limit");
        $user = $request->user();
        $params['student_id'] = $user->id;
        $result = AdminMessage::getMessage($params);
        return $this->resJson(ErrorCode::SUCCESS, $result);
    }

    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/student/messages",
     *     summary="我发送的消息通知",
     *     @OA\Parameter(name="search", in="query", description="搜索"),
     *     @OA\Parameter(name="page", in="query", description="每页数量"),
     *     @OA\Parameter(name="limit", in="query", description="每页数量"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="meta", type="object", description="元信息",
     *                 @OA\Property(property="count", type="integer", description="当前页的项目数"),
     *                 @OA\Property(property="perPage", type="integer", description="每页显示的项目数"),
     *                 @OA\Property(property="currentPage", type="integer", description="当前页码"),
     *                 @OA\Property(property="lastPage", type="integer", description="最后一页的页码"),
     *                 @OA\Property(property="total", type="integer", description="总数")
     *             ),
     *             @OA\Property(property="list", type="array", description="数据列表", @OA\Items(type="object",
     *                 @OA\Property(property="id", type="integer", description="唯一标识"),
     *                 @OA\Property(property="content", type="string", description="内容"),
     *                 @OA\Property(property="teacher_name", type="string", description="教师"),
     *                 @OA\Property(property="student_name", type="string", description="学生"),
     *                 @OA\Property(property="created_at", type="string", description="创建时间")
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     *
     * @RequestParam(fields={"page": 1, "limit": 10 })
     */
    public function getTeacherMessageList(Request $request)
    {
        $params = $request->only("search", "page", "limit");
        $user = $request->user();
        $params['student_id'] = $user->id;
        $result = TeacherMessage::getMessage($params);
        return $this->resJson(ErrorCode::SUCCESS, $result);
    }
}
