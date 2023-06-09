<?php

namespace App\Admin\Controllers;

use App\Models\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Encore\Admin\Show;

class UsersController extends Controller
{
    use ModelForm;

    /**
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
           // 页面标题
           $content->header('用户列表');
           $content->body($this->grid());
        });
    }

    /**
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) {
           $content->header('header');
           $content->description('description');
           $content->body($this->form()->edit($id));
        });
    }

    /**
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header('header');
            $content->description('description');
            $content->body($this->form());
        });
    }

    /**
     * @return Grid
     */
    protected function grid()
    {
        // 根据回调函数，在页面上用表格的形式展示用户记录
        return Admin::grid(User::class, function (Grid $grid) {
           // 创建一个列名为 ID 的列，内容是用户的 id 字段，并且可以在前端页面点击排序
           $grid->id('ID')->sortable();
           // 创建一个列名为 用户名 的列，内容是用户的 name 字段。下面的 email() 和 created_at() 同理
           $grid->name('用户名');
           $grid->email('邮箱');
           $grid->email_verified('已验证邮箱')->display(function ($value) {
              return $value ? '是' : '否';
           });
           $grid->created_at('注册时间');
            // 不在页面显示 `新建` 按钮，因为我们不需要在后台新建用户
           $grid->disableCreateButton();
           $grid->actions(function ($actions) {
               // 不在每一行后面展示查看按钮
              $actions->disableView();
               // 不在每一行后面展示删除按钮
              $actions->disableDelete();
               // 不在每一行后面展示编辑按钮
              $actions->disableEdit();
           });
           $grid->tools(function ($tools) {
               // 禁用批量删除按钮
               $tools->batch(function ($batch) {
                   $batch->disableDelete();
               });
           });
        });
    }

    /**
     * @return Form
     */
    protected function form()
    {
        return Admin::form(User::class, function (Form $form) {
           $form->display('id', 'ID');
           $form->display('created_at', 'Created At');
           $form->display('updated_at', 'Updated At');
        });
    }
}
