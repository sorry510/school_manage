<?php

namespace App\Http\Controllers\Teacher;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\LineUserRelation;
use App\Models\Teacher;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    /**
     * @OA\get(
     *     tags={"教师"},
     *     path="/api/teacher/info",
     *     summary="个人信息",
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="id", type="string", description="id"),
     *             @OA\Property(property="email", type="string", description="邮箱"),
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
        $user = $request->user('teachers');
        $user->roles = ['teacher'];
        $user->hasBindLine = !!LineUserRelation::where('relation_id', $user->id)
            ->where('type', LineUserRelation::TYPE_TEACHER)
            ->first();
        return $this->resJson(ErrorCode::SUCCESS, $user);
    }

    /**
     * @OA\post(
     *     tags={"教师"},
     *     path="/api/teacher/bind-line",
     *     summary="绑定line",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="line_id", type="string", description="line_id"),
     *                 required={"line_id"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function bindTeacher(Request $request)
    {
        $params = $request->only('line_id');
        $user = $request->user('teachers');
        try {
            $hasOne = LineUserRelation::where('line_user_id', $params['line_id'])
                ->where('type', LineUserRelation::TYPE_TEACHER)->first(); // 查阅是否已经绑定了其它教师
            if ($hasOne) {
                return $this->resJson(ErrorCode::ERROR, '已经绑定了其它教师');
            }
            LineUserRelation::create([
                'line_user_id' => $params['line_id'],
                'relation_id' => $user->id,
                'type' => LineUserRelation::TYPE_TEACHER,
            ]);
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }
}
