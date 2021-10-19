<?php

namespace App\Models\Lottery\Traits\Relationship;

use App\Models\Order\Order;

trait LotteryRelationship
{
 	/**
     * 订单
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
