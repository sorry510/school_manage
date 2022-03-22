<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherMessage extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_message', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('teacher_id')->default(0)->comment('教师id');
            $table->integer('student_id')->default(0)->comment('学生id');
            $table->longText('content')->comment('内容');
            $table->integer('status')->default(2)->comment('接收状态[1:已接收,2:未接收]');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table teacher_message is '教师消息推送表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('teacher_message');
    }
}
