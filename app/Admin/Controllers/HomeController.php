<?php

namespace App\Admin\Controllers;

use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Chart\Bar;
use App\Admin\Widgets\Line;
use Encore\Admin\Widgets\InfoBox;
use Encore\Admin\Widgets\Tab;
use Encore\Admin\Widgets\Table;
use App\Models\User\User;
use App\Models\Order\Order;
use App\Models\Recharge\Recharge;
use App\Models\Atm\Atm;
use App\Models\AccountDetail\AccountDetail;
use Illuminate\Support\Facades\DB;
use App\Models\AdminUser\AdminUser;
use App\Models\AdminRole\AdminRole;


class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('后台统计');
            $content->description(' ');

            $today = date('Y-m-d');  
            
            $content->row(function ($row) use ($today) {
                $count =  User::noAnalog()->count(); 
                $row->column(3, new InfoBox('总会员', 'users', 'aqua', '/admin/manage/user', $count));
                $count = AccountDetail::noAnalog()->sum('account_amount');
                $row->column(3, new InfoBox('资金流水', 'file', 'green', '/admin/manage/account', $count));
                $count = Order::noAnalog()->count();
                $row->column(3, new InfoBox('总订单', 'book', 'maroon', admin_url('manage/order'), $count));
                $count = Atm::where('state',Atm::STATE_PASS)->noAnalog()->sum('price');
                $row->column(3, new InfoBox('总提现', 'atm', 'teal', admin_url('manage/atm'), $count));
            });
            $content->row(function (Row $row) {
                $row->column(1/2, function (Column $column) {
                    $markets = collect(Order::LH_TYPE) ;
                    $orders = Order::select('lh_type', DB::Raw("COUNT(*) AS total_num"))
                                   ->noAnalog()
                                   ->groupBy('lh_type')
                                   ->get()
                                   ->pluck('total_num', 'lh_type')
                                   ->all();
                    $marketData = $markets->map(function ($item, $key) use ($orders) {
                        return isset($orders[$key]) ? $orders[$key] : 0;
                    });
                     
                    $data = [
                        'labels' => array_values($markets->all()),
                        'data' => array_values($marketData->all()),
                    ];
                    $column->append((new Box('订单分布[龙/虎]', view('admin.charts.doughnut', compact('data')))));
                });

                $row->column(1/2, function (Column $column) {
                    $markets = collect(Order::ORDER_TYPE) ;
                    $orders = Order::select('type', DB::Raw("COUNT(*) AS total_num"))
                                   ->noAnalog()
                                   ->groupBy('type')
                                   ->get()
                                   ->pluck('total_num', 'type')
                                   ->all();
                    $marketData = $markets->map(function ($item, $key) use ($orders) {
                        return isset($orders[$key]) ? $orders[$key] : 0;
                    });
                     
                    $data = [
                        'labels' => array_values($markets->all()),
                        'data' => array_values($marketData->all()),
                    ];
                    $column->append((new Box('订单分布[类型]', view('admin.charts.pie', compact('data')))));
                });

            });



            $content->row(function (Row $row) {
 
                $day = 10;
                for ($i=0; $i < $day; $i++) { 
                    $days[] = date('Y-m-d', strtotime('-' . $i . ' day'));
                }
                sort($days);
                $start = $days[0];
                $end = end($days);

                $barDays = $days;
                // 支出
                $decrease = AccountDetail::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') AS time, sum(account_amount) AS price"))
                        ->where('account_type', AccountDetail::ACCOUNT_DECREASE)
                        ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '>=', $start)
                        ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '<=', $end)
                        
                        ->noAnalog()
                        ->groupBy('time')
                        ->get();
                    foreach ($decrease as $key => $value) {
                        $_decrease[$value['time']] = $value['price'];
                    }

                    // 收入
                    $increase = AccountDetail::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') AS time, sum(account_amount) AS price"))
                        ->where('account_type', AccountDetail::ACCOUNT_INCREASE)
                        ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '>=', $start)
                        ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '<=', $end)
                        
                        ->noAnalog()
                        ->groupBy('time')
                        ->get();
                    foreach ($increase as $key => $value) {
                        $_increase[$value['time']] = $value['price'];
                    }

                    foreach ($barDays as $key => $value) {
                        $_increase[$value] = empty($_increase[$value]) ? 0 : $_increase[$value];
                        $_decrease[$value] = empty($_decrease[$value]) ? 0 : $_decrease[$value];
                        $barDays[$key] = preg_replace('/^[0-9]{4}-/', '', $value);
                    }
                    ksort($_increase);
                    ksort($_decrease);
                $data = [
                    'labels' => $barDays,
                    'datasets' => [
                        'increase' => array_values($_increase),
                        'decrease' => array_values($_decrease),
                    ],
                ];
                $row->column(6, new Box('十日收支情况', view('admin.charts.bar', compact('data'))));

               

                // 十日投注情况
                $lineDays = $days;
                $member = Order::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') AS time, count(*) AS count"))
                                ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '>=', $start)
                                ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '<=', $end)
                                ->noAnalog()
                                ->groupBy('time')
                                ->pluck('count', 'time')
                                ->toArray();
                foreach ($lineDays as $key => $value) {
                    $member[$value] = empty($member[$value]) ? 0 : $member[$value];
                    $lineDays[$key] = preg_replace('/^[0-9]{4}-/', '', $value);
                }
                ksort($member);
                $data = [
                    'labels' => $lineDays,
                    'datasets' => array_values($member),
                ];
                $row->column(6, new Box('十日投注情况', view('admin.charts.line', compact('data'))));
            });
            
        });
    }
}
