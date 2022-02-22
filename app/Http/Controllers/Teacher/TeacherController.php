<?php

namespace App\Http\Controllers\Teacher;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Mail\TeacherInvitationMail;
use App\Models\School;
use App\Models\SchoolApply;
use App\Models\SchoolTeacher;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\TeacherMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Sorry510\Annotations\RequestParam;

class TeacherController extends Controller
{

    /**
     * @OA\post(
     *     tags={"教师"},
     *     path="/api/teacher/schools",
     *     summary="申请学校",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="学校名称"),
     *                 required={"name"}
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
    public function applySchool(Request $request)
    {
        $params = $request->only("name");
        $user = $request->user("teachers");
        try {

            $data = [
                "name" => $params['name'],
                "teacher_id" => $user->id,
                "status" => SchoolApply::STATUS_PENDING, // [1:已成功,2:被拒绝,3:进行中]
            ];
            $result = SchoolApply::create($data);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/teacher/schools-apply",
     *     summary="申请的学校列表",
     *     @OA\Parameter(name="search", in="query", description="查询信息(学校名称)"),
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
     *                 @OA\Property(property="name", type="string", description="学校名称"),
     *                 @OA\Property(property="status", type="integer", description="申请状态"),
     *                 @OA\Property(property="created_at", type="string", description="创建时间"),
     *                 @OA\Property(property="updated_at", type="string", description="更新时间")
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     *
     * @RequestParam(fields={"page": 1, "limit": 10 })
     */
    public function applySchoolList(Request $request)
    {
        $params = $request->only("search", "limit");
        $user = $request->user("teachers");
        try {
            $query = SchoolApply::select('id', 'name', 'status')->where('teacher_id', $user->id);
            if (is_effective($params, 'search')) {
                $query->where('name', 'like', "%{$params["search"]}%");
            }
            $result = $query->paginate($params["limit"]);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/teacher/schools",
     *     summary="学校列表",
     *     @OA\Parameter(name="search", in="query", description="查询信息(学校名称)"),
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
     *                 @OA\Property(property="name", type="string", description="学校名称"),
     *                 @OA\Property(property="remark", type="string", description="备注"),
     *                 @OA\Property(property="created_at", type="string", description="创建时间"),
     *                 @OA\Property(property="updated_at", type="string", description="更新时间")
     *             ))
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     *
     * @RequestParam(fields={"page": 1, "limit": 10 })
     */
    public function getSchoolList(Request $request)
    {
        $params = $request->only("search", "limit");
        $user = $request->user("teachers");
        try {
            $schoolIds = SchoolTeacher::where('teacher_id', $user->id)->pluck('school_id');
            $query = School::whereIn('id', $schoolIds);
            if (is_effective($params, 'search')) {
                $query->where('name', 'like', "%{$params["search"]}%");
            }
            $result = $query->paginate($params["limit"]);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     tags={"教师"},
     *     path="/api/teacher/invitation",
     *     summary="邀请教师",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="teacher_id", type="string", description="教师id"),
     *                 @OA\Property(property="school_id", type="string", description="学校id"),
     *                 required={"teacher_id", "school_id"}
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
    public function inviteTeacher(Request $request)
    {
        $params = $request->only("teacher_id", "school_id");
        $user = $request->user('teacher');
        try {
            if (!SchoolTeacher::getAdminInfo($user->id, $params['school_id'])) {
                $this->resJson(ErrorCode::NO_AUTH);
            }
            $school = School::where('id', $params['school_id'])->value('name');
            $email = Teacher::where('id', $params['teacher_id'])->value('email');
            if ($email && $school) {
                $secret = md5($params['teacher_id'] . $params['school_id'] . millisecond());
                TeacherMail::create([
                    'teacher_id' => $params['teacher_id'],
                    'school_id' => $params['school_id'],
                    'secret' => $secret,
                ]);
                // TODO 改为队列方式
                \Mail::to($email)->send(new TeacherInvitationMail([
                    'school' => $school,
                    'teacher_id' => $params['teacher_id'],
                    'school_id' => $params['school_id'],
                    'secret' => $secret,
                ]));
                return $this->resJson(ErrorCode::SUCCESS);
            }
            $this->resJson(ErrorCode::ERROR);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/teacher/accept",
     *     summary="接受邀请",
     *     @OA\Parameter(name="teacher_id", in="query", description="学校id"),
     *     @OA\Parameter(name="school_id", in="query", description="学校id"),
     *     @OA\Parameter(name="secret", in="query", description="学校id"),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function acceptInvitation(Request $request)
    {
        $params = $request->only("teacher_id", "school_id", "secret");
        try {
            $check = TeacherMail::where('teacher_id', $params['teacher_id'])->where('school_id', $params['school_id'])->where('secret', $params['secret'])->first();
            if ($check) {
                $find = SchoolTeacher::where('teacher_id', $params['teacher_id'])->where('school_id', $params['school_id'])->first();
                if (!$find) {
                    // 成功邀请
                    SchoolTeacher::create([
                        'teacher_id' => $params['teacher_id'],
                        'school_id' => $params['school_id'],
                        'teacher_type' => SchoolTeacher::TYPE_NORMAL,
                    ]);
                    return view('welcome');
                } else {
                    // 已经邀请过了，不能重复邀请
                    return view('welcome');
                }
            }
            // 验证是伪造
            return view('welcome');
        } catch (\Throwable $e) {
            return view('404');
        }
    }

    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/teacher/teachers",
     *     summary="老师列表",
     *     @OA\Parameter(name="search", in="query", description="查询信息(老师名称，email)"),
     *     @OA\Parameter(name="school_id", in="query", description="学校id"),
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
     *                 @OA\Property(property="name", type="string", description="教师"),
     *                 @OA\Property(property="email", type="string", description="email"),
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
        $params = $request->only("search", "school_id", "limit");
        try {
            $query = Teacher::select('id', 'name', 'email', 'created_at');
            if (is_effective($params, 'search')) {
                $query->where(function ($query) use ($params) {
                    $query->where('name', 'like', "%{$params["search"]}%")
                        ->orWhere('email', 'like', "%{$params["search"]}%");
                });
            }
            if (is_effective($params, 'school_id')) {
                $teacherIds = SchoolTeacher::where('school_id', $params['school_id'])->pluck('teacher_id');
                $query->whereIn('id', $teacherIds);
            }
            $result = $query->paginate($params["limit"]);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\post(
     *     tags={"教师"},
     *     path="/api/teacher/students",
     *     summary="添加学生",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", description="姓名"),
     *                 @OA\Property(property="account", type="string", description="用户名"),
     *                 @OA\Property(property="password", type="string", description="密码"),
     *                 @OA\Property(property="school_id", type="integer", description="学校id"),
     *                 required={"name", "account", "password", "school_id"}
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
    public function addStudent(Request $request)
    {
        $params = $request->only("name", "account", "password", "school_id");
        $user = $request->user('teacher');
        try {
            if (!SchoolTeacher::getAdminInfo($user->id, $params['school_id'])) {
                $this->resJson(ErrorCode::NO_AUTH);
            }
            $data = [
                "name" => $params['name'],
                "account" => $params['account'],
                "school_id" => $params['school_id'],
                "password" => Hash::make($params['password']),
            ];
            // 写入用户表
            $student = Student::create($data);
            return $this->resJson(ErrorCode::SUCCESS, $student);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/teacher/students",
     *     summary="学生列表",
     *     @OA\Parameter(name="search", in="query", description="查询信息(老师名称，email)"),
     *     @OA\Parameter(name="school_id", in="query", description="学校id"),
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
     *                 @OA\Property(property="name", type="string", description="教师"),
     *                 @OA\Property(property="email", type="string", description="email"),
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
    public function getStudentList(Request $request)
    {
        $params = $request->only("search", "school_id", "limit");
        try {
            $query = Student::select('id', 'name', 'school_id', 'created_at');
            if (is_effective($params, 'search')) {
                $query->where('name', 'like', "%{$params["search"]}%");
            }
            if (is_effective($params, 'school_id')) {
                $query->where('school_id', $params['school_id']);
            }
            $result = $query->paginate($params["limit"]);
            return $this->resJson(ErrorCode::SUCCESS, $result);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }
}
