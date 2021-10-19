<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Input;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use App\Models\Order\Order;
use App\Models\Lottery\Lottery;
use App\Models\AdminUser\AdminUser;
use Encore\Admin\Widgets\Callout;
use App\Admin\Extensions\Tools\Diff;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\MessageBag;


class OrderController extends Controller
{
    use ModelForm,
        FilterQuery;

    const MODEL = Order::class;    

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('投注列表');
            $content->description(' ');

            $content->body($this->grid());
            $this->condition($content, function ($query, Row $row) {
                $query = $query->diff(Request::get('diff'));
                if (! Admin::user()->isAdministrator()) $query = $query->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
                $total = $query->sum('amount');
                $describe = "<h4>总投注：%s 元</h4>";
                $format = sprintf($describe, number_format($total, 2));
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
        return Admin::grid(Order::class, function (Grid $grid) {
            $grid->model()->diff(Request::get('diff'));
            if (! Admin::user()->isAdministrator()) {
                $grid->model()->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
            }
            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->user()->username('用户名')->display(function ($value) {
                return '<a href="'.admin_url('manage/user?id='.$this->user_id).'">'. $value .'</a>';
            });
            $grid->lottery_id('投注期数')->display(function($value) {
                $periods = Lottery::where('id', $value)->value('periods');
                return '<a href="'.admin_url('manage/lottery?periods='.$periods).'">'. $periods .'</a>';
            });
            $grid->amount('投注金额');
            $grid->type('投注类型')->display(function($value) {
                return Order::ORDER_TYPE[$value];
            });
            $grid->lh_type('竞猜类型')->display(function($value) {
                return Order::LH_TYPE[$value];
            });
            //$grid->barrier('关数')->help('第几关');
            $grid->barrier('关数')->display(function ($value) {
                return Order::ORDER_BARRIER[$value];
            });
            $grid->status('状态')->display(function ($value) {
                return Order::STATUS_TEXT[$value];
            });
            $grid->is_win('开奖结果')->display(function($value) {
                if ($value == Order::WIN) {
                    $text = '<font color="green">赢</font>';
                }elseif ($value == Order::WIN_NO) {
                    $text = '<font color="red">输</font>';
                }else{
                    $text = Order::WIN_TEXT[$value];
                }
                return $text;
            });
            $grid->profit('收益金额');
            $grid->award_number('开奖数字');
            $grid->updated_at('结算时间');
            
            $grid->disableActions();
            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableRowSelector();
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
            $filter->equal('user_id', '客户标识');
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('name', $this->input);
                });
            }, '姓名');

            $filter->like('order_sn', '订单编号');
            $filter->between('created_at', '起止日期')->datetime([
                'format' => 'YYYY-MM-DD HH:mm',
            ]);
        };
    }

}
