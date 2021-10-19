<?php

namespace App\Http\Controllers\Home;

use Request;
use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Lottery\Lottery;
use App\Models\Rank\Rank;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{

	/**
     * 首页
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth()->user();
        $orders = Order::currentUser()->latest()->take(9)->get();
        $lotterys = Lottery::end()->latest()->take(30)->get();
        $end_last = Lottery::end()->latest()->first();
        $periods = Lottery::where('status', 1)->latest()->value('periods');
        $end_time = Lottery::where('status', 1)->latest()->value('end_time');
        if ($end_time) $end_time = str_replace("-","/",$end_time);
        $lottery_id = Lottery::where('status', 1)->latest()->value('id');
        $rank = Rank::next($user->pass)->first();
        $orderType = Order::ORDER_TYPE;
        $lhType = Order::LH_TYPE;
        $winType = Order::WIN_TEXT;
        $barrier = Order::ORDER_BARRIER;
        $late = Order::currentUser()->latest()->first();
        if ($late) {
            $locktype = $late->type;
        }else{
            $locktype = 1;
        }
        return view('home.index.index', compact('orders', 'user', 'lotterys', 'rank', 'end_last', 'periods', 'end_time', 'lottery_id', 'orderType', 'lhType', 'winType', 'barrier', 'locktype'));
    }

    /**
     * 统计
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function statistics()
    {
        $data = Request::all();
        $orders = Order::currentUser()->where('status', 2)->where('lottery_id', $data['id'])->first();
        if ($orders) {
            $is_win = Order::currentUser()->where('status', 2)->where('lottery_id', $data['id'])->value('is_win');
            if ($is_win == Order::WIN) {
                $return['msg'] = '下注成功，请继续闯关';
            }elseif ($is_win == Order::WIN_NO) {
                $return['msg'] = '下注失败，请查看收益';
            }elseif ($is_win == Order::WIN_DRAW) {
                $return['msg'] = '本关为和，系统撤单请继续闯关';
            }
        }else{
            $return['msg'] = '本期未下注';
        }
        return responseJson('', true, $return);
    }

    /**
     * 号码走势
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function trend()
    {
        $lotterys = Lottery::end()->latest()->paginate(25);
        return view('home.index.trend', compact('lotterys'));
    }

    /**
     * 新手指导
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function guide()
    {
        return view('home.index.guide');
    }


    
}
