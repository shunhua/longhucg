<?php

namespace App\Models\Atm\Traits\Relationship;

use App\Models\User\User;

/**
 * Class ConfigMarketRelationship.
 */
trait AtmRelationship
{
    /**
     * 用户
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
