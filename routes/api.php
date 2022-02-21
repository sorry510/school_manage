<?php

use Illuminate\Http\Request;
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

/**
 * 教师接口
 */
Route::middleware(['auth:teachers'])->prefix('teacher')->group(function () {
    Route::post('schools', 'Teacher\\TeacherController@applySchool'); // 申请学校
    Route::get('schools', 'Teacher\\TeacherController@getSchoolList'); // 学校列表
    Route::get('teachers', 'Teacher\\TeacherController@getTeacherList'); // 学校老师
    Route::post('students', 'Teacher\\TeacherController@addStudent'); // 添加学生
    Route::get('students', 'Teacher\\TeacherController@getStudentList'); // 学生列表
});

/**
 * 学生接口
 */
Route::middleware(['auth:students'])->get('/test2', function (Request $request) {
    echo 1;die;
    return $request->user('students');
});
