<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Lottery\Lottery;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Callout;
use Illuminate\Support\Facades\Request;

class LotteryController extends Controller
{
    use ModelForm,
        FilterQuery;
        
    /**
     * Associated Repository Model.
     */
    const MODEL = Lottery::class;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('彩票列表');
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
            
            $statements_type = Lottery::STATUS_TEXT;
            $new_statements_type = [];
            foreach ($statements_type  as $key => $value) {
                $new_statements_type[$key + 1] = $value;
            }
            $filter->equal('periods', '彩票期数');
            // $filter->where(function ($query) {
            //     $input = $this->input;
            //     if (!empty($input)) {
            //         $query->where('status', $input - 1);
            //     }
            // }, '开奖状态')->select($new_statements_type);
        };
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Lottery::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID')->sortable();
            $grid->periods('彩票期数');
            $grid->start_time('开始时间');
            $grid->end_time('开奖时间'); 
            $grid->status('状态')->display(function($value) {
                return $value == Lottery::STATUSING ? '<font color="red">'.Lottery::STATUS_TEXT[$value].'</font>' : Lottery::STATUS_TEXT[$value];
            });
            $grid->open_number('开奖数字');

            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->disableActions();
            //$grid->disableFilter();
            $grid->filter($this->filter());

        });
    }

}
