<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Recharge\Recharge;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Callout;
use App\Models\AdminUser\AdminUser;
use Illuminate\Support\Facades\Request;

class RechargeController extends Controller
{
    use ModelForm,
        FilterQuery;
        
    /**
     * Associated Repository Model.
     */
    const MODEL = Recharge::class;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('充值管理');
            $content->description(' ');

            $content->body($this->grid());        
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
            // 去掉默认的id过滤器
            $filter->disableIdFilter();
            $filter->column(1/2, function ($filter) {
               $filter->equal('user_id', '客户标识');
               $filter->where(function ($query) {
                   $query->whereHas('user', function ($query) {
                       $query->where('username', $this->input);
                   });
               }, '用户名');
               $filter->between('created_at', '充值时间')->datetime([
                    'format' => 'YYYY-MM-DD HH:mm',
                ]);
            });
                
        };
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Recharge::class, function (Grid $grid) {
            $grid->model()->latest();
            $grid->model()->diff(Request::get('diff'));
            if (! Admin::user()->isAdministrator()) {
                $grid->model()->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
            }

            $grid->id('ID')->sortable();
            $grid->user()->username('用户名')->display(function ($value) {
                return '<a href="'.admin_url('manage/user?id='.$this->user_id).'">'. $value .'</a>';
            });
            $grid->trade_no('订单号');
            $grid->amount('充值金额');
            $grid->created_at('充值时间');


            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableActions();

            $grid->filter($this->filter());

        });
    }

}
