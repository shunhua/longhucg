<?php

namespace App\Admin\Controllers;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Atm\Atm;
use Illuminate\Support\MessageBag;
use App\Models\AccountDetail\AccountDetail;
use App\Repositories\AccountDetailRepository;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Encore\Admin\Widgets\Callout;
use App\Models\AdminUser\AdminUser;
use App\Models\User\User;
use App\Admin\Extensions\Tools\Diff;
use Illuminate\Support\Facades\Request;
use App\Admin\Extensions\Tools\BatchAudit;
use Yansongda\Pay\Exceptions\BusinessException;

class AtmController extends Controller
{
    use ModelForm,
        FilterQuery;
        
    /**
     * Associated Repository Model.
     */
    const MODEL = Atm::class;

    /**
     * @var AccountDetailRepository
     */
    protected $accountDetail;
    
    /**
     * RegisterController constructor.
     *
     * @param AccountDetailRepository $accountDetail
     */
    public function __construct(AccountDetailRepository $accountDetail)
    {
        $this->accountDetail = $accountDetail;
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('提现管理');
            $content->description(' ');

            $content->body($this->grid());

            $this->condition($content, function ($query, Row $row) {
                $query = $query->diff(Request::get('diff'));
                if (! Admin::user()->isAdministrator()) $query = $query->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
                $totalAtm = $query->sum('price');
                $describe = "<h4>总提现： %s 元&nbsp;&nbsp; </h4>";
                $format = sprintf($describe, number_format($totalAtm, 2));
                $row->column(12, function (Column $column) use ($format) {
                    $column->append((new Callout($format))->style('info'));
                });
            });         
        });
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

            $content->header('审核提现');
            $content->description('');

            $content->body($this->form()->edit($id));
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
            $filter->equal('user_id', '客户标识');
            $filter->where(function ($query) {
                $query->whereHas('user', function ($query) {
                    $query->where('username', $this->input);
                });
            }, '用户名');
            $filter->equal('state', '提现状态')->select(Atm::STATE_TEXT);
           $filter->between('created_at', '提现时间')->datetime([
               'format' => 'YYYY-MM-DD HH:mm',
           ]);
            
        };
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Atm::class, function (Grid $grid) {
            
            $grid->model()->latest();
            $grid->model()->diff(Request::get('diff'));
            if (! Admin::user()->isAdministrator()) {
                $grid->model()->whereIn('parent_admin', AdminUser::ownChildrenId(Admin::user()->id));
            }

            $grid->id('ID');
            $grid->user()->username('用户名')->display(function ($value) {
                return '<a href="'.admin_url('manage/user?id='.$this->user_id).'">'. $value .'</a>';
            });
            $grid->user()->name('姓名');
            $grid->price('申请金额');
            $grid->real_amount('提现金额');
            $grid->state('申请状态')->display(function ($value) {
                $text = Atm::STATE_TEXT[$value];
                if ($value == Atm::STATE_PASS) return '<font color="green">' . $text . '</font>';
                if ($value == Atm::STATE_REJECT) return '<font color="red">' . $text . '</font>';
                return $text;
            });
            $grid->created_at('提现时间');

            $grid->disableExport();
            $grid->disableRowSelector();
            $grid->disableCreation();
            $grid->actions(function ($actions) {
                $actions->disableDelete();
                $actions->disableView();
                if (! Admin::user()->isAdministrator()) $actions->disableEdit();
                if ($actions->row->state != Atm::STATE_NORMAL) {
                    $actions->disableEdit();
                } 
            });

            $grid->filter($this->filter());

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Atm::class, function (Form $form) {

            $form->hidden('id');
            $form->display('bank', '开户行');
            $form->display('card', '银行卡号');
            $form->display('name', '持卡姓名');
            $form->display('price', '提现金额');
            $states = [
                'on'  => ['value' => 1, 'text' => '通过', 'color' => 'success'],
                'off' => ['value' => 0, 'text' => '驳回', 'color' => 'danger'],
            ];
            $form->switch('state', '审核')->states($states);
            $form->textarea('remark', '备注')->rows(10);

            $form->hidden('bank_code');
            $form->hidden('trade_no');
            
            $form->saving(function(Form $form) {
                if ($form->model()->state != Atm::STATE_NORMAL) {
                    $error = new MessageBag([
                        'title'   => '提示',
                        'message' => '请勿重复审核',
                    ]);
                    return back()->with(compact('error'));
                }
                // 默认驳回
                $form->state = $form->state == ATM::STATE_NORMAL ? ATM::STATE_REJECT : $form->state;
                 
            });

            //保存后回调
            $form->saved(function (Form $form) {
                $atm = $form->model();
                if ($atm->state == Atm::STATE_REJECT) {
                    $param = [
                        'account_type' => AccountDetail::ACCOUNT_INCREASE,
                        'account_amount' => $atm->price,
                        'relationship_id' => $atm->id,
                        'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_ATM_REJECT,
                        'user_id' => $atm->user_id,
                        'parent_admin' => $atm->parent_admin,
                        'analog' => $atm->analog
                    ];
                    $this->accountDetail->create($param);
                } 

            });

        });
    }


     /**
     * 发送post请求
     * @param string $url 请求地址
     * @param array $post_data post键值对数据
     * @return string
     */
   public function sendPost($url, $post_data) {
     
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
     
        return $result;
    }

    public function dfnotify()
    {
        $data = Request::all();
        \Log::info('------admin-notify------' . serialize($data));

        $md5_data = [
            'merchant_no' => '168666999001647',
            'out_trade_no' => $data['out_trade_no'],
            'pay_num' => $data['pay_num'],
            'total_fee' => $data['amount'],
            'key' => 'tiv3vnwtds7xe6iibemdgvxs121kw44z', //商户秘钥
        ];
        $sign = strtoupper(md5(join("", $md5_data)));

        if ($sign == $data['sign'] && $data['trade_result'] == 'success' && $data['return_code'] == '10000') {
            $atm = Atm::where('trade_no', $data['pay_num'])->first();
            if ($atm['bank_code'] == '2') {
                $atm->bank_code = 1;
                $atm->save();
            }
        }
        exit('success');
    }

    /**
     * 批量审核
     *
     * @param Request $request
     * @return void
     */
    public function batchAudit()
    {
        if (! Request::has('ids')) return;
        foreach (Atm::find(Request::post('ids')) as $atm) {
            if ($atm->state == Atm::STATE_NORMAL) {
                // 通过
                if (Request::post('action') === '1') {
                    $atm->state = ATM::STATE_PASS; 
                    if ($atm->save()) {
                        // 提现三级奖励
                        $inviteTiers = \App\Repositories\UserRepository::getInviteTiers($atm->user_id, 3);
                        $this->accountDetail->grantAtmReward($inviteTiers, $atm->price, $atm->id);
                    }
                // 驳回
                } else {
                    $atm->state = ATM::STATE_REJECT;
                    if ($atm->save()) {
                        $param = [
                            'account_type' => AccountDetail::ACCOUNT_INCREASE,
                            'account_amount' => $atm->price,
                            'relationship_id' => $atm->id,
                            'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_ATM_REJECT,
                            'user_id' => $atm->user_id,
                            'parent_admin' => $atm->parent_admin,
                            'analog' => $atm->analog
                        ];
                        $this->accountDetail->create($param);
                    }
                }
            }
        }
    }

}
