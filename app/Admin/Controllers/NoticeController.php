<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Models\Notice\Notice;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\DB;
use App\Models\User\User;

class NoticeController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('消息列表');
            $content->description(' ');

            $content->body($this->grid());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Notice::class, function (Grid $grid) {

            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->user()->username('接收者')->display(function ($value) {
                return '<a href="'.admin_url('manage/user?id='.$this->user_id).'">'. $value .'</a>';
            });
            $grid->contents('内容')->limit(30);
            $grid->status('状态')->display(function ($value) {
                return $value == 1 ? '<font color="green">已读</font>' : '未读';
            });
            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableFilter();
            $grid->disableColumnSelector();
            $grid->actions(function ($actions) {
                $actions->disableView();
                if (! Admin::user()->isAdministrator()) $actions->disableCreation();
                if (! Admin::user()->isAdministrator()) $actions->disableEdit();
                if (! Admin::user()->isAdministrator()) $actions->disableDelete();
            });
            $grid->filter($this->filter());
        });
    }

    /**
     * 条件
     *
     * @return Closure
     */
    protected function filter()
    {
        return function($filter) {
            $filter->useModal();
            $filter->between('created_at', '起止日期')->datetime([
                'format' => 'YYYY-MM-DD HH:mm',
            ]);
        };
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('修改消息');
            $content->description('');

            $content->body($this->form($id)->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('添加消息');
            $content->description('');

            $content->body($this->form());
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id='')
    {
        return Admin::form(Notice::class, function (Form $form) use ($id) {
            $form->select('user_id', '接收者')->options(function ($user_id) {
                return User::pluck('username', 'id');
            })->rules('required');
            $form->textarea('contents', '消息内容')->rows(6)->rules('required');
        });
    }

}
