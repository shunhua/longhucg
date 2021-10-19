<?php

namespace App\Repositories;

use App\Models\Order\Order;
use App\Models\User\User;
use App\Models\UserLevel\UserLevel;
use App\Models\Lottery\Lottery;
use App\Models\Rank\Rank;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use GuzzleHttp\Client;
use Carbon\Carbon;
use App\Models\AccountDetail\AccountDetail;
use App\Repositories\AccountDetailRepository;

/**
 * Class OrderRepository.
 */
class OrderRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Order::class;

        /**
     * @var AccountDetailRepository
     */
    protected $accountDetail;
        
    /**
     * AtmController constructor.
     *
     * @param AccountDetailRepository $accountDetail
     */
    public function __construct(AccountDetailRepository $accountDetail) 
    {
        $this->accountDetail = $accountDetail;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $user = auth()->user();
       
        if ((date('H', time()) >= 8 && date('H', time()) <19) ){
         if ($user->isFreeze()) throw new GeneralException('账号已冻结');
         if(!isset($data['lottery_id']) || !$data['lottery_id']) throw new GeneralException('参数错误');
         $exists = Order::where('user_id', $user->id)->where('lottery_id', $data['lottery_id'])->exists();
         if($exists) throw new GeneralException('每期只能下注一次');
         // if (in_array($data['barrier'], [5,6,7,8])) {
         //    $last4 = Order::currentUser()->where('is_win', Order::WIN)->where('barrier', $data['barrier']-1)->latest()->first();
         //    if ($last4 && ($data['lottery_id'] == ($last4->lottery_id + 1)))  throw new GeneralException('下一期才可以继续闯关');
         // }
         $uids = User::where('group', $user->group)->pluck('id')->toArray();
         $group = Order::whereIn('user_id', $uids)->where('lottery_id', $data['lottery_id'])->where('type', Order::TYPE[$data['type']])->exists();
         if($group) throw new GeneralException('同组会员每期不能下注同一类型');
        }else{
            throw new GeneralException('不在下单时间段');
        }
        if ((!$user->pass) && date('H', time()) >= 16) {
            throw new GeneralException('未闯关用户,不在下单时间段');
        }
        
        $order = self::MODEL;
        $order = new $order;
        $order->user_id = $user->id;
        $order->parent_admin = $user->parent_admin;
        $order->analog = $user->analog;
        $order->amount = $data['amount'];
        $order->type = Order::TYPE[$data['type']];
        $order->lh_type = Order::LHTYPE[$data['lh_type']];
        $order->barrier = $data['barrier'];
        $order->lottery_id = $data['lottery_id'];
        $order->status = Order::STATUSING;
        if ($order->barrier == 1 && $user->level_id ==1 && $order->amount == 30) throw new GeneralException('请联系客服,升级会员');
        if($order->amount > $user->balance || $user->balance < 0) throw new GeneralException('余额不足,请充值');
        DB::transaction(function () use ($order, $user) {
            if ((!$user->rank) && $order->barrier==1) {
                if ($order->amount == 50) {
                    $user->rank = 1;
                }elseif ($order->amount == 30) {
                    $user->rank = 2; 
                }
                $user->save();
            }
            if ($order->save()) {
                $periods = Lottery::where('id', $order->lottery_id)->value('periods');
                // 写入流水
                $param = [
                    'account_type' => AccountDetail::ACCOUNT_DECREASE,
                    'account_amount' => $order->amount,
                    'relationship_id' => $order->id,
                    'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_ORDER,
                    'parent_admin' => $order->parent_admin,
                    'analog' => $order->analog,
                    'remark' => '第'.$periods.'期'
                ];
                $this->accountDetail->create($param);

                return $order->id;
            }
            
        });
    } 

    /**
     * 开奖结算 （开奖）
    */
    public function handle()
    { 
        while (true) {
            $nowCarbon = strtotime(Carbon::now());
            $stayLottery = Lottery::where('status', 1)->first();
            if ($stayLottery) {
                // echo Carbon::now()."\r\n";
                $endTime = strtotime($stayLottery->end_time);
                // 倒计时结束 开奖
                if (($endTime - $nowCarbon) <= 0) {
                    // $apidata = self::caiApi();
                    // if ($apidata) {
                    //     // 开奖数字
                    //     if ($stayLottery->periods == $apidata['expect']) {
                    //         $num = $apidata['opencode'];
                    //     }else{
                    //         $num = self::caiDateApi($stayLottery->periods);
                    //     }
                        $num = self::caiDateApi($stayLottery->periods);
                        if (!$num) {
                            self::handle();
                        }
                        // 发放奖励
                        self::profit($stayLottery, $num);
                        // 更新本期结束
                        $update = [
                            'status' => Lottery::STATUS_END,
                            'open_number' => $num,
                        ];
                        Lottery::where('id', $stayLottery->id)->update($update);
                    }

                //}
            }else{ 
                // 下一期
                self::nextIssue();
            }
            // 延迟 1s
            usleep(200000);     
        }
        
    }

    /**
     * 开奖及发奖
    */
    public function profit($stayLottery, $openNum)
    {
        $orders = Order::settlementNo()->where('lottery_id', $stayLottery->id)->get();
        $orders->each(function ($item, $key) use ($openNum) {
            $user = User::find($item->user_id);
            $arrNum = explode(',', $openNum); 
            switch ($item->type) {
                case 1:
                   $resylt = self::lhResult($arrNum[0], $arrNum[1]);
                   break;
                case 2:
                   $resylt = self::lhResult($arrNum[0], $arrNum[2]);
                   break;
                case 3:
                   $resylt = self::lhResult($arrNum[0], $arrNum[3]);
                    break;
                case 4:
                   $resylt = self::lhResult($arrNum[0], $arrNum[4]);
                    break;
                case 5:
                   $resylt = self::lhResult($arrNum[1], $arrNum[2]);
                    break;
                case 6:
                   $resylt = self::lhResult($arrNum[1], $arrNum[3]);
                    break;
                case 7:
                   $resylt = self::lhResult($arrNum[1], $arrNum[4]);
                    break;
                case 8:
                   $resylt = self::lhResult($arrNum[2], $arrNum[3]);
                    break;
                case 9:
                   $resylt = self::lhResult($arrNum[2], $arrNum[4]);
                    break;
                case 10:
                   $resylt = self::lhResult($arrNum[3], $arrNum[4]);
                    break;    
            }
            $profit = 0;
            $remark = '';
            if ($resylt == Order::WIN_DRAW) {
                $is_win = Order::WIN_DRAW;
                $user->pass = $user->pass;
                $profit = $item->amount;
                $remark = '本关为和,退还金额'; 
            }else{
                if ($resylt == $item->lh_type) {
                    $is_win = Order::WIN;
                    if ($item->barrier == 8){
                        $user->pass = 0;
                        $user->rank = 0;
                    }else{
                        $user->pass = $item->barrier;
                    }
                    $profit = $item->amount * 1.956;
                    $remark = '闯关成功,返利收益'; 
                }else{
                    $is_win = Order::WIN_NO;
                    $pro = 0;
                    $attach = 0;
                    if ($user->rank == 1) {
                       $pro = Rank::where('id', $item->barrier)->value('profit_1');
                       $attach = Rank::where('id', $item->barrier)->value('attach_1');
                    }elseif ($user->rank == 2) {
                       $pro = Rank::where('id', $item->barrier)->value('profit_2'); 
                       $attach = Rank::where('id', $item->barrier)->value('attach_2');
                    }
                    $profit = $pro + $attach;
                    $user->pass = 0;
                    $user->rank = 0;
                    $remark = '闯关失败,返利收益';
                }  
            }

            $item->status = Order::STATUS_END;
            $item->is_statements = Order::SETTLEMENT;
            $item->award_number = $openNum;
            $item->is_win = $is_win;
            $item->profit = $profit;
            DB::transaction(function () use ($item, $user, $profit, $remark) {
                if ($item->save()) {
                    // 更新通关数
                    $user->save();
                    if (!empty($profit)) {
                        $account = [
                            'account_type' => AccountDetail::ACCOUNT_INCREASE,
                            'account_amount' => $profit,
                            'relationship_id' => $item->id,
                            'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_PROFIT,
                            'parent_admin' => $item->parent_admin,
                            'analog' => $item->analog,
                            'user_id' => $item->user_id,
                            'remark' => $remark
                        ];
                        $this->accountDetail->create($account);
                    }
                    
                }  
                return true;              
            });

        });
    }


    /**
     * 获取结果
    */
    public function lhResult($num1, $num2)
    {
        if ($num1 > $num2) {
            return 1; //龙
        }elseif ($num1 < $num2) {
            return 2; //虎
        }else{
            return 3; //和
        }
    } 

    /**
     * 下一期期数开始
    */
    public function nextIssue()
    {
        $apidata = self::caiApi();
        if ($apidata) {
            $last = substr($apidata['expect'], -2);//判断最后一期
            $interval_time = 23;//默认20分钟一期 + 1分钟的延迟[稳定获取结果]
            $periods = $apidata['expect'] + 1;
            if ($last == '59') {
                //新一天的新一期开始
                $periods = date("Ymd",strtotime("+1 day")).'001';
                $interval_time = 43;
            }
            if ($last == '09') {
                $interval_time = (4*60) +  $interval_time;
            }
            $start_time = $apidata['opentime'];
            $create = [
                'periods' => $periods,
                'start_time' => $start_time,
                'end_time' => date("Y-m-d H:i:s",strtotime($start_time)+($interval_time*60)), 
            ];
            Lottery::create($create); 
        }
        
    }  

    /**
     * 重庆时时彩(欢乐生肖)api
    */
    public function caiApi()
    {
        $http = new Client;
        $response = $http->get('http://api.b1api.com/api?p=json&t=cqssc&limit=1&token=94DEC7CF602EB9F1');
        $response = json_decode((string) $response->getBody(), true);
        if ($response){
            return $response['data'][0];    
        }else{
            return false; 
        }
    }  

    /**
     * 重庆时时彩(欢乐生肖)
     * 按日期查询:获取指定一期的开奖数字
     * aparm $lottery 指定期数
     * return string 开奖数字
    */
    public function caiDateApi($lottery)
    {
        $date = substr($lottery, 0, -3);
        $qishu = (int) substr($lottery, -2);
        $http = new Client;
        $response = $http->get('http://api.b1api.com/api?p=json&t=cqssc&token=94DEC7CF602EB9F1&date='.$date);
        $response = json_decode((string) $response->getBody(), true);
        //if ($response)  $data = array_reverse($response['data'])[$qishu-1];
        if ($response) {
            foreach ($response['data'] as $key => $value) { 
                if ($lottery == $value['expect']){
                    return $value['opencode'];
                }else{
                    continue;
                    return null; 
                    // dd($value);
                } 
            } 
        }
          
    }  

}
