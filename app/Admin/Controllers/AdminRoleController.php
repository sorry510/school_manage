<?php

namespace App\Admin\Controllers;

use Encore\Admin\Controllers\RoleController;
use Encore\Admin\Form;

class AdminRoleController extends RoleController
{
    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form()
    {
        $permissionModel = config('admin.database.permissions_model');
        $roleModel = config('admin.database.roles_model');

        $form = new Form(new $roleModel());

        $form->display('id', 'ID');

        $form->text('slug', trans('admin.slug'))->rules('required');
        $form->text('name', trans('admin.name'))->rules('required');
        $form->listbox('permissions', trans('admin.permissions'))
            ->settings([
                'preserveSelectionOnMove' => 'moved',
                'moveOnSelect' => false,
            ]) // https://www.virtuosoft.eu/code/bootstrap-duallistbox/
            ->options($permissionModel::all()->pluck('name', 'id'));

        $form->display('created_at', trans('admin.created_at'));
        $form->display('updated_at', trans('admin.updated_at'));

        return $form;
    }
}
