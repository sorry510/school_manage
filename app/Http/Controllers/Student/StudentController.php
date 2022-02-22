<?php

namespace App\Http\Controllers\Student;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Models\StudentTeacherLike;
use Illuminate\Http\Request;

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
            if (StudentTeacherLike::where('teacher_id', $params['teacher_id'])->where('user_id', $user->id)->first()) {
                return $this->resJson(ErrorCode::REPEAT_OPERATION);
            }
            StudentTeacherLike::create([
                'teacher_id' => $params['teacher_id'],
                'user_id' => $user->id,
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
            if (!StudentTeacherLike::where('teacher_id', $params['teacher_id'])->where('user_id', $user->id)->first()) {
                return $this->resJson(ErrorCode::REPEAT_OPERATION);
            }
            StudentTeacherLike::where([
                'teacher_id' => $params['teacher_id'],
                'user_id' => $user->id,
            ])->delete();
            return $this->resJson(ErrorCode::SUCCESS);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    public function getTeacherList(Request $request)
    {
        $user = $request->user();
        return $this->resJson(ErrorCode::SUCCESS, $user);
    }
}
