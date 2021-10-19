<?php

namespace App\Models\AccountDetail\Traits\Relationship;

use App\Models\User\User;

/**
 * Class ConfigMarketRelationship.
 */
trait AccountDetailRelationship
{
    /**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
