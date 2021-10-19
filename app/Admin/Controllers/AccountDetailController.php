<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\AccountDetail\AccountDetail;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Callout;
use App\Models\AdminUser\AdminUser;
use App\Admin\Extensions\Tools\Diff;
use Illuminate\Support\Facades\Request;

class AccountDetailController extends Controller
{
    use ModelForm,
        FilterQuery;

    /**
     * Associated Repository Model.
     */
    const MODEL = AccountDetail::class;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('收支明细');
            $content->description(' ');
            $content->body($this->grid());

            $this->condition($content, function ($query, Row $row) {
                $query = $query->diff(Request::get('diff'));
                if (! Admin::user()->isAdministrator()) $query = $query->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
                $income = clone $query;
                $expenditure = clone $query;
                // 收入
                $income = $income->where('account_type', AccountDetail::ACCOUNT_INCREASE)->sum('account_amount');
                // 支出
                $expenditure = $expenditure->where('account_type', AccountDetail::ACCOUNT_DECREASE)->sum('account_amount');
                $describe = "<h4>客户收入：%s 元&nbsp;&nbsp; 客户支出：%s 元&nbsp;&nbsp; </h4>";
                $format = sprintf($describe, number_format($income, 2), number_format($expenditure, 2));
                $row->column(12, function (Column $column) use ($format) {
                    $column->append((new Callout($format))->style('info'));
                });
            });
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(AccountDetail::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->model()->diff(Request::get('diff'));
            
            if (! Admin::user()->isAdministrator()) {
                $grid->model()->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
            }

            $grid->id('ID')->sortable();
            $grid->user()->username('用户名')->display(function ($value) {
                return '<a href="'.admin_url('manage/user?id='.$this->user_id).'">'. $value .'</a>';
            });
            $grid->account_type('收支类型')->display(function ($value) {
                return '<font color="' . ($value == AccountDetail::ACCOUNT_INCREASE ? 'red' : 'green') . '">' . AccountDetail::ACCOUNT[$value] . '</font>';
            });
            $grid->account_amount('交易金额')->sortable();
            $grid->balance('结余');
            $grid->relationship_type('用途')->display(function ($value) {
                return AccountDetail::RELATIONSHIP_TYPE[$value];
            });
            $grid->remark('详情');
            $grid->created_at('收支时间');

            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableActions();


            $grid->filter($this->filter());

        });
    }

    /**
     * 条件查询
     *
     * @return Closure
     */
    protected function filter()
    {
        return function($filter) {
            $filter->column(1/2, function ($filter) {
                $filter->equal('user_id', '客户标识');
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('name', $this->input);
                    });
                }, '姓名');
                $filter->where(function ($query) {
                    $query->whereHas('user', function ($query) {
                        $query->where('mobile', $this->input);
                    });
                }, '手机号');
            });

            $filter->column(1/2, function ($filter) {
                $filter->where(function ($query) {
                    $query->whereIn('parent_admin', AdminUser::ownChildrenId((int) $this->input));
                }, '代理标识');
                $filter->equal('relationship_type', '收支类型')->select(AccountDetail::RELATIONSHIP_TYPE);
                $filter->between('created_at', '起止日期')->datetime([
                    'format' => 'YYYY-MM-DD HH:mm',
                ]);
            });
             
        };
    }

}
