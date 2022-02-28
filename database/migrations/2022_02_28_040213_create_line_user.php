<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLineUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('line_user', function (Blueprint $table) {
            $table->string('id')->comment('id');
            $table->string('name')->default('')->comment('name');
            $table->string('avatar')->default('')->comment('avatar');
            $table->string('email')->default('')->comment('email');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table line_user is 'line用户表'");

        Schema::create('line_user_relation', function (Blueprint $table) {
            $table->string('id')->comment('id');
            $table->string('line_user_id')->comment('line_user_id');
            $table->integer('relation_id')->comment('relation_id');
            $table->integer('type')->default(1)->comment('推送类型[1:教师,2:学生]');
            $table->dateTime('created_at')->nullable()->comment('创建时间');
            $table->dateTime('updated_at')->nullable()->comment('更新时间');
        });
        DB::statement("comment on table line_user_relation is 'line用户关联教师学生表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
