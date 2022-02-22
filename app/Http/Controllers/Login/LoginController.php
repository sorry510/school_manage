<?php

namespace App\Http\Controllers\Login;

use App\Constants\ErrorCode;
use App\Http\Controllers\Controller;
use App\Http\Validates\LoginValidate;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Sorry510\Annotations\Validator;

class LoginController extends Controller
{
    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/login-in",
     *     summary="用户登录",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", description="用户名"),
     *                 @OA\Property(property="password", type="string", description="密码"),
     *                 @OA\Property(property="type", type="string", description="类型[1:教师,2:学生]"),
     *                 required={"username", "password", "type"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据",
     *             @OA\Property(property="token", type="string", description="用户token"),
     *         ),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     *
     * @Validator(class=LoginValidate::class, scene="user")
     */
    public function loginIn(Request $request)
    {
        // 接受参数
        $params = $request->only("username", "password", "type");
        try {
            $errorCode = ErrorCode::SUCCESS;
            $data = [];
            if ($params['type'] === 'teacher') {
                [$errorCode, $data] = $this->loginTeacher($params);
            } else if ($params['type'] === 'student') {
                [$errorCode, $data] = $this->loginStudent($params);
            } else {
                $errorCode = ErrorCode::ERROR;
            }
            return $this->resJson($errorCode, $data);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    protected function loginTeacher($data)
    {
        // 获取用户信息
        $user = Teacher::where('email', $data['username'])->first();
        if (!$user) {
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        // 验证用户是否被启用
        if ($user->status != "1") {
            return [ErrorCode::USER_FREEZE, ""];
        }
        // 记录登录信息
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->last_login_ip = request()->ip();

        // 验证密码
        if (!Hash::check($data['password'], $user->password)) {
            // 失败次数加一
            $user->login_failure += 1;
            $user->save();
            return [ErrorCode::LOGIN_ERROR, ""];
        }
        // 失败次数清零
        $user->login_failure = 0;
        $user->online = 1;
        $user->save();
        $token = $user->createToken($data["type"]);
        return [ErrorCode::SUCCESS, ['token' => $token->accessToken, 'type' => 'teacher']];
    }

    protected function loginStudent($data)
    {
        // 获取用户信息
        $user = Student::where('account', $data['username'])->first();
        if (!$user) {
            return [ErrorCode::LOGIN_ERROR, ""];
        }

        // 记录登录信息
        $user->last_login_time = date("Y-m-d H:i:s");
        $user->last_login_ip = request()->ip();

        // 验证密码
        if (!Hash::check($data['password'], $user->password)) {
            // 失败次数加一
            $user->login_failure += 1;
            $user->save();
            return [ErrorCode::LOGIN_ERROR, ""];
        }

        // 失败次数清零
        $user->login_failure = 0;
        $user->online = 1;
        $user->save();
        $token = $user->createToken($data["type"]);
        return [ErrorCode::SUCCESS, ['token' => $token->accessToken, 'type' => 'student']];
    }

    /**
     * @OA\delete(
     *     tags={"用户认证"},
     *     path="/api/login-out",
     *     summary="退出登录",
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function loginOut(Request $request)
    {
        Auth::logout();
        return $this->resJson(ErrorCode::SUCCESS);
    }

    /**
     * @OA\post(
     *     tags={"用户认证"},
     *     path="/api/register",
     *     summary="教师注册",
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", description="用户名"),
     *                 @OA\Property(property="password", type="string", description="密码"),
     *                 required={"username", "password"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Success", @OA\JsonContent(
     *         @OA\Property(property="code", type="integer", description="返回码"),
     *         @OA\Property(property="message", type="string", description="错误信息"),
     *         @OA\Property(property="data", type="object", description="返回数据"),
     *         @OA\Property(property="extra", type="object", description="返回额外数据"),
     *         @OA\Property(property="timestamp", type="integer", description="服务器响应的时间戳")
     *     ))
     * )
     */
    public function registerTeacher(Request $request)
    {
        $params = $request->only("name", "email", "password");
        try {
            // 注册验证
            $signRes = $this->checkRegister($params);
            if ($signRes != ErrorCode::SUCCESS) {
                return $this->resJson($signRes);
            }

            $data = [
                "name" => $params['name'],
                "email" => $params['email'],
                "password" => Hash::make($params['password']),
                "status" => 1, // 直接激活
            ];
            $user = Teacher::create($data);
            // TODO 注册成功，发送邮箱
            return $this->resJson(ErrorCode::SUCCESS, $user);
        } catch (\Throwable $e) {
            return $this->resJson(ErrorCode::ERROR, $e->getMessage());
        }
    }

    protected function checkRegister($data): int
    {
        // 验证邮箱是否已注册
        if (Teacher::where('email', $data['email'])->first()) {
            return ErrorCode::EMAIL_EXIST;
        }
        return ErrorCode::SUCCESS;
    }
}
