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
use Illuminate\Support\Facades\DB;
use Encore\Admin\Widgets\Callout;
use Illuminate\Support\Facades\Request;
use App\Models\User\User;
use App\Models\UserLevel\UserLevel;
use App\Models\AdminUser\AdminUser;
use App\Repositories\IncreaseRepository;
use App\Repositories\ReduceRepository;
use Illuminate\Support\MessageBag;
use App\Admin\Extensions\Tools\Diff;

class UserController extends Controller
{
    use ModelForm,
        FilterQuery;

    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;  

     /**
     * @var IncreaseRepository
     */
    protected $increase;

    /**
     * @var ReduceRepository
     */
    protected $reduce; 


    /**
     * RegisterController constructor.
     *
     * @param IncreaseRepository $increase
     */
    public function __construct(IncreaseRepository $increase, ReduceRepository $reduce)
    {
        $this->increase = $increase;
        $this->reduce = $reduce;
    } 

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('用户管理');
            $content->description(' ');
            $content->body($this->grid());
            
            $this->condition($content, function ($query, Row $row) {
                $query = $query->diff(Request::get('diff'));
                if (! Admin::user()->isAdministrator()) $query = $query->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
                $balance = $query->sum('balance');
                $describe = "<h4>总余额：%s 元</h4>";
                $format = sprintf($describe, number_format($balance, 2));
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
        return Admin::grid(User::class, function (Grid $grid) {
            $grid->model()->orderBy('id', 'desc');
            $grid->id('ID');
            $grid->username('用户名');
            $grid->name('姓名'); 
            $grid->level()->name('会员级别');
            $grid->balance('余额')->sortable();
            $grid->group('组名');
            $grid->rank('投注档')->display(function ($value) {
                if ($value) return $value == 1 ? '50' : '30';
            });
            $grid->pass('闯关数')->display(function ($value) {
                if ($value) return $value;
            });
            $states = [
                'on'  => ['value' => 1, 'text' => '冻结', 'color' => 'danger'],
                'off' => ['value' => 0, 'text' => '正常', 'color' => 'success'],
            ];
            if (Admin::user()->can('user.index.freeze')) $grid->freeze('冻结')->switch($states);
            
            $grid->last_login_at('最后登录时间');
            $grid->last_ip('最后登录IP');
            $grid->created_at('注册时间');

            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableView();
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

            $filter->column(1/2, function ($filter) {
               $filter->useModal();
               $filter->equal('username', '用户名');
               $filter->equal('name', '姓名');
            });
            $filter->column(1/2, function ($filter) {
                $filter->equal('mobile', '手机');
                $filter->between('created_at', '注册日期')->datetime([
                    'format' => 'YYYY-MM-DD HH:mm',
                ]);
            });
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

            $content->header('修改会员');
            $content->description(' ');

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

            $content->header('添加会员');
            $content->description(' ');

            $content->body($this->form());
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form($id=0)
    {
        return Admin::form(User::class, function (Form $form) use ($id) {

            $form->tab('基础信息', function (Form $form) {
                $task = UserLevel::pluck('name', 'id')->toArray();
                $form->select('level_id', '级别')->options($task)->rules('required');
                $states = [
                    'on'  => ['value' => 1, 'text' => '冻结', 'color' => 'danger'],
                    'off' => ['value' => 0, 'text' => '正常', 'color' => 'success'],
                ];
                if (Admin::user()->can('user.index.freeze')) $form->switch('freeze', '冻结')->states($states);
                $form->text('name', '姓名');
                $form->mobile('mobile', '手机');
            })->tab('修改金额', function (Form $form) {
                $form->currency('increase', '充值金额')->symbol('￥');
                $form->currency('reduce', '扣除金额')->symbol('￥');
            })->tab('修改密码', function (Form $form) {
                // 支付密码
                $form->password('pay_password', '支付密码')->rules('confirmed')
                ->default(function ($form) {
                    return $form->model()->pay_password;
                });
                $form->password('pay_password_confirmation', '确认支付密码')
                    ->default(function ($form) {
                        return $form->model()->pay_password;
                    });
                $form->ignore(['pay_password_confirmation']);

                $form->password('password', trans('admin.password'))->rules('required|confirmed')
                ->default(function ($form) {
                    return $form->model()->password;
                });
                $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                    ->default(function ($form) {
                        return $form->model()->password;
                    });
                $form->ignore(['password_confirmation']);
            });

            $form->saving(function (Form $form) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }

                if ($form->pay_password && $form->model()->pay_password != $form->pay_password) {
                    $form->pay_password = bcrypt($form->pay_password);
                }

                if (isset($error)) {
                    $error = new MessageBag([
                        'title'   => '提示',
                        'message' => $error,
                    ]);
                    return back()->with(compact('error'));
                }

                $user_id = $form->model()->id;
                $increase = (float) $form->increase; 
                $form->input('increase', '0');
                $reduce = (float) $form->reduce; 
                $form->input('reduce', '0');

                if (! empty($increase)) {
                    $data = [
                        'user_id' => $user_id,
                        'amount' => $increase,
                        'admin_id' => Admin::user()->id,
                    ];
                    $this->increase->create($data);
                }

                if (! empty($reduce)) {
                    $data = [
                        'user_id' => $user_id,
                        'amount' => $reduce,
                        'admin_id' => Admin::user()->id,
                    ];
                    $this->reduce->create($data);
                }

            });

        });
    }

}
