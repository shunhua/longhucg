<?php

namespace App\Http\Controllers\Home;

use Request;
use App\Models\Atm\Atm;
use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Notice\Notice;
use App\Models\Recharge\Recharge;
use App\Models\AccountDetail\AccountDetail;
use App\Repositories\UserRepository;
use App\Repositories\AccountDetailRepository;
use App\Repositories\CardRepository;
use App\Repositories\AtmRepository;
use App\Repositories\RechargeRepository;


class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $atm;
    protected $user;
    protected $card;
    protected $accountDetail;
    
    /**
     * RegisterController constructor.
     *
     * @param UserRepository $user
     */
    public function __construct(UserRepository $user, AccountDetailRepository $accountDetail, CardRepository $card,AtmRepository $atm,RechargeRepository $recharge)
    {
        $this->atm = $atm;
        $this->user = $user;
        $this->card = $card;
        $this->recharge = $recharge;
        $this->accountDetail = $accountDetail;
    }

	/**
     * 首页
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::currentUser()->latest()->paginate(8);
        $orderType = Order::ORDER_TYPE;
        $lhType = Order::LH_TYPE;
        $winType = Order::WIN_TEXT;
        $barrier = Order::ORDER_BARRIER;
        return view('home.user.index', compact('orders', 'orderType', 'lhType', 'winType', 'barrier'));
    }

    /**
     * 完善信息
     */
    public function save()
    {
        $user = Auth()->user();
        return view('home.user.save', compact('user'));
    }

    /**
     * 保存信息
     */
    public function saveinfo()
    {
        if ($this->user->saveinfo(Auth()->user(), Request::all())) return responseJson('',true);
    }

    /**
     * 消息中心
     */
    public function notice()
    {
        $notices = Notice::where('user_id',Auth()->id())->orderBy('status')->latest()->limit(10)->get();
        return view('home.user.notice', compact('notices'));
    }

    /**
     * 标记已读
    */
    public function markRead()
    {
        Notice::where('id',Request::input('id'))->update(['status' => 1]);return responseJson('',true);
    }

    /**
     * 余额明细
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function account()
    {
        $accounts = Auth()->user()->accounts()->latest()->paginate(8);
        return view('home.user.account', compact('accounts'));
    }

    /**
     * 充值明细
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function recharge()
    {
        $accounts = Auth()->user()->accounts()->recharge()->latest()->paginate(8);
        return view('home.user.recharge', compact('accounts'));
    }

    /**
     * 充值
     *
     */
    public function pay()
    {
        return view('home.user.pay');
    }

     /**
     * 提现明细
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function withdraw()
    {
        $accounts = Auth()->user()->accounts()->atm()->latest()->paginate(8);
        $card = Auth()->user()->cards()->first();
        return view('home.user.withdraw', compact('accounts','card'));
    }

    /**
     * 绑卡
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function bindcard()
    {
        return view('home.user.bindcard');
    }

    /**
     * 保存卡信息
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function savebank()
    {
        $this->card->create(Request::all()); return responseJson('绑定成功', true);
    }

    /**
     * 解绑卡
     *
     * @return array
     */
    public function cardRemove()
    {
       $this->card->query()->where('id',Request::input('id'))->delete();return responseJson('解绑成功', true);
    }

    /**
     * 提现
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function atm()
    {
        $card = Auth()->user()->cards()->first();return view('home.user.atm', compact('card'));
    }

    /**
     * 提现提交
     *
     * @return JsonResponse
     */
    public function atmSub()
    {
        $this->atm->create(Request::input());return responseJson('提交成功', true);
    }

    /**
     * 充值提交
     *
     * @return JsonResponse
     */
    public function paySub()
    {
        $this->recharge->create(Request::input());return responseJson('提交成功', true);
    }
    
}
