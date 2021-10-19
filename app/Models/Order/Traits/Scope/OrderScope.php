<?php

namespace App\Models\Order\Traits\Scope;

trait OrderScope
{
    /**
     * 当前会员
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentUser($query)
    {
        return $query->where('user_id', Auth()->id());
    }

    /**
     * 订单状态
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * 没结算的订单
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSettlementNo($query)
    {
        return $query->where('is_statements', self::SETTLEMENT_NO);
    }

}
