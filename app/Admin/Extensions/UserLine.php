<?php

namespace App\Admin\Extensions;

use Encore\Admin\Form\Field;
use App\Models\Settlement\Settlement;
use Illuminate\Support\Facades\DB;

class UserLine extends Field
{
    protected $view = 'admin.charts.user-line';

    public function render()
    {
        $user_id = $this->value;

        $day = 10;
        for ($i=0; $i < $day; $i++) { 
            $days[] = date('Y-m-d', strtotime('-' . $i . ' day'));
        }
        sort($days);
        $start = $days[0];
        $end = end($days);

        $barDays = $days;
        $settlements = Settlement::select(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d') AS time, sum(price) AS total_price"))
                                 ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '>=', $start)
                                 ->where(DB::raw("DATE_FORMAT(created_at, '%Y-%m-%d')"), '<=', $end)
                                 ->where('user_id', $user_id)
                                 ->noAnalog()
                                 ->groupBy('time')
                                 ->pluck('total_price', 'time')
                                 ->toArray();
        foreach ($barDays as $key => $value) {
            $settlements[$value] = empty($settlements[$value]) ? 0 : $settlements[$value];
            $barDays[$key] = preg_replace('/^[0-9]{4}-/', '', $value);
        }
        ksort($settlements);
        $this->variables['line_labels'] = $barDays;
        $this->variables['line_datas'] = array_values($settlements);

        return parent::render();
    }
}