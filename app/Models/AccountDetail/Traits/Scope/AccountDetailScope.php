<?php

namespace App\Models\AccountDetail\Traits\Scope;

use App\Models\AccountDetail\AccountDetail;

trait AccountDetailScope
{
    /**
     * 系统充值
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecharge($query)
    {
        return $query->where('relationship_type', AccountDetail::RELATIONSHIP_TYPE_INCREASE);
    }

    /**
     * 提现
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAtm($query)
    {
        return $query->where('relationship_type', AccountDetail::RELATIONSHIP_TYPE_ATM);
    }

}
