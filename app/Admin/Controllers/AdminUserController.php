<?php

namespace App\Admin\Controllers;

use App\Admin\Models\Admin;
use Encore\Admin\Controllers\UserController;
use Encore\Admin\Form;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends UserController
{
    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $userModel = config('admin.database.users_model');
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $userModel());

        $userTable = config('admin.database.users_table');
        $connection = config('admin.database.connection');

        $form->display('id', 'ID');
        $form->text('username', trans('admin.username'))
            ->creationRules(['required', "unique:{$connection}.{$userTable}"])
            ->updateRules(['required', "unique:{$connection}.{$userTable},username,{{id}}"]);

        $form->text('name', trans('admin.name'))->rules('required');
        $form->image('avatar', trans('admin.avatar'));

        $userId = request()->route()->parameter('user');
        $options = [];
        if ($userId) {
            $options["uploadUrl"] = '/admin/auth/users/files/' . $userId;
        }
        $form->multipleImage('imgs', '图片列表')->options($options)->removable(); // 多选图片

        $form->password('password', trans('admin.password'))->rules('required|confirmed');
        $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
            ->default(function ($form) {
                return $form->model()->password;
            });

        $form->ignore(['password_confirmation']);

        $form->multipleSelect('roles', trans('admin.roles'))->options($roleModel::all()->pluck('name', 'id'));
        $form->multipleSelect('permissions', trans('admin.permissions'))->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        $form->saving(function (Form $form) {
            if ($form->password && $form->model()->password != $form->password) {
                $form->password = Hash::make($form->password);
            }
        });

        return $form;
    }

    public function updateFile(Request $request, $id = null)
    {
        $imgs = $request->file('imgs');
        $file_id = $request->file_id;
        $data = [];
        if (!empty($imgs)) {
            $path = $imgs[0]->store('images', 'admin');
            $data[$file_id] = $path;
        }
        $user = Admin::where('id', $id)->first();
        $newImgs = array_merge($user->imgs, $data);
        $user->imgs = $newImgs;
        $user->save();

        $url = config('filesystems.disks.admin.url');
        $img = end($newImgs);
        $key = key($newImgs);
        return [
            'initialPreview' => [
                "{$url}/$img", // 预览地址
            ],
            'initialPreviewConfig' => [
                [
                    "caption" => pathinfo($img)['basename'],
                    "key" => $key,
                    "url" => "/admin/auth/users/{$id}", // 删除接口
                    "downloadUrl" => "{$url}/$img", // 下载地址
                ],
            ],
            'append' => true,
        ];
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        return $this->form()->update($id);
    }
}
