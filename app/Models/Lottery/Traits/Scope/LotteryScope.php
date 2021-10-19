<?php

namespace App\Models\Lottery\Traits\Scope;

trait LotteryScope
{

    /**
     * 未开奖
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIng($query)
    {
        return $query->where('status', self::STATUSING);
    }

    /**
     * 已开奖
     * @return \Illuminate\Database\Eloquent\Builder
    */
    public function scopeEnd($query)
    {
        return $query->where('status', self::STATUS_END);
    }

}
