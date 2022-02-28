<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateSchoolTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('school', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->index()->comment('名称');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->index()->comment('删除时间');
        });
        DB::statement("comment on table school is '学校表'");

        Schema::create('school_apply', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->index()->comment('名称');
            $table->string('teacher_id', 255)->nullable()->comment('备注');
            $table->integer('status')->default(3)->comment('状态[1:已成功,2:被拒绝,3:进行中]');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table school_apply is '学校申请表'");

        Schema::create('teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('用户名');
            $table->string('email')->unique()->comment('email(登录)');
            $table->string('password')->comment('密码');
            $table->integer('status')->default(2)->comment('状态[1:已激活,2:未激活]');
            $table->integer('online')->default(2)->comment('状态[1:在线,2:离线]');
            $table->tinyInteger('login_failure')->default(0)->comment('登录失败次数');
            $table->dateTime('last_login_time')->nullable()->comment('最后登录时间');
            $table->string('last_login_ip', 20)->nullable()->comment('最后登录ip');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->index()->comment('删除时间');
        });
        DB::statement("comment on table teacher is '教师表'");

        Schema::create('school_teacher', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->comment('教师id');
            $table->integer('teacher_type')->default(2)->comment('教师类型[1:管理员,2:普通]');
            $table->integer('school_id')->comment('学校id');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table school_teacher is '学校教师关系表'");

        Schema::create('student', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50)->comment('姓名');
            $table->string('account', 50)->comment('用户名(登录)');
            $table->string('password')->comment('密码');
            $table->integer('online')->default(2)->comment('状态[1:在线,2:离线]');
            $table->tinyInteger('login_failure')->default(0)->comment('登录失败次数');
            $table->dateTime('last_login_time')->nullable()->comment('最后登录时间');
            $table->string('last_login_ip', 20)->nullable()->comment('最后登录ip');
            $table->integer('school_id')->comment('学校id');
            $table->string('remark', 255)->nullable()->comment('备注');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
            $table->dateTime('deleted_at')->nullable()->index()->comment('删除时间');
        });
        DB::statement("comment on table student is '学生表'");

        Schema::create('student_teacher_like', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->comment('教师id');
            $table->integer('student_id')->comment('学生id');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table student_teacher_like is '学生关注教师表'");

        Schema::create('student_teacher_message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->comment('教师id');
            $table->integer('student_id')->comment('学生id');
            $table->integer('direction')->comment('交流方向[1:教师to学生,2:学生to教师]');
            $table->longText('content')->comment('内容');
            $table->integer('status')->default(2)->comment('接收状态[1:已接收,2:未接收]');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table student_teacher_message is '消息表'");

        Schema::create('admin_message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('admin_id')->comment('admin_id');
            $table->integer('type')->default(1)->comment('推送类型[1:广播,2:教师,3:学生]');
            $table->string('teacher_ids')->default('')->comment('教师ids');
            $table->string('student_ids')->default('')->comment('学生ids');
            $table->longText('content')->comment('内容');
            $table->integer('status')->default(2)->comment('接收状态[1:已接收,2:未接收]');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table admin_message is '管理员消息推送表'");

        Schema::create('teacher_mail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->comment('推荐教师id');
            $table->integer('school_id')->comment('推荐学校id');
            $table->string('secret')->default('')->comment('秘钥');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table teacher_mail is '教师邮件邀请表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('school');
        Schema::dropIfExists('school_apply');
        Schema::dropIfExists('teacher');
        Schema::dropIfExists('school_teacher');
        Schema::dropIfExists('student');
        Schema::dropIfExists('student_teacher_like');
        Schema::dropIfExists('student_teacher_message');
        Schema::dropIfExists('admin_message');
        Schema::dropIfExists('teacher_mail');
    }
}
