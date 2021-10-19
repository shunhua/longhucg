<?php

namespace App\Models\User\Traits;

/**
 * Class UserAccess.
 */
trait UserAccess
{

    /**
     * 是否冻结
     *
     * @return boolean
     */
    public function isFreeze()
    {
        return $this->freeze === self::STATE_FREEZE;
    }

}
