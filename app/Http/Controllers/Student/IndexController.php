<?php

namespace App\Http\Controllers\Student;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * @OA\get(
     *     tags={"学生"},
     *     path="/api/student/info",
     *     summary="个人信息",
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="id", type="string", description="id"),
     *             @OA\Property(property="account", type="string", description="用户"),
     *             @OA\Property(property="name", type="string", description="姓名"),
     *             @OA\Property(property="roles", type="array", description="角色", @OA\Items(type="string", description="唯一标识")),
     *             @OA\Property(property="created_at", type="string", description="创建时间"),
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳"),
     *         required={"code", "message", "data", "timestamp"}
     *     ))
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user('students');
        $user->roles = ['student'];
        return $this->resJson(ErrorCode::SUCCESS, $user);
    }
}
