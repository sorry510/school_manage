<?php

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */
Route::post('/login-in', 'Login\\LoginController@loginIn'); // 登录
Route::post('/register', 'Login\\LoginController@registerTeacher'); // 教师注册
Route::get('/teacher/accept', 'Teacher\\TeacherController@acceptInvitation'); // 老师接受邀请

/**
 * 教师接口
 */
Route::middleware(['auth:teachers'])->prefix('teacher')->group(function () {
    Route::get('info', 'Teacher\\IndexController@index'); // 个人信息

    Route::post('schools', 'Teacher\\TeacherController@applySchool'); // 申请学校
    Route::get('schools-apply', 'Teacher\\TeacherController@applySchoolList'); // 申请的学校列表
    Route::get('schools', 'Teacher\\TeacherController@getSchoolList'); // 学校列表

    Route::get('invitation', 'Teacher\\TeacherController@inviteTeacher'); // 邀请教师
    Route::get('teachers', 'Teacher\\TeacherController@getTeacherList'); // 老师列表

    Route::post('students', 'Teacher\\TeacherController@addStudent'); // 添加学生
    Route::get('students', 'Teacher\\TeacherController@getStudentList'); // 学生列表
});

/**
 * 学生接口
 */
Route::middleware(['auth:students'])->prefix('student')->group(function () {
    Route::post('info', 'Student\\IndexController@index'); // 个人信息

    Route::post('like', 'Student\\StudentController@like'); // 关注教师
    Route::post('unlike', 'Student\\StudentController@unlike'); // 取消关注教师
});
