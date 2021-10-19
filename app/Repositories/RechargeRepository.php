<?php

namespace App\Repositories;

use App\Models\Recharge\Recharge;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Carbon\Carbon;

/**
 * Class RechargeRepository.
 */
class RechargeRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Recharge::class;

    /**
     * @param array $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $recharge = self::MODEL;
        $recharge = new $recharge;
        $recharge->user_id = auth()->id();
        $recharge->parent_admin = auth()->user()->parent_admin;
        $recharge->analog = auth()->user()->analog;
        $recharge->trade_no = $data['trade_no'];
        $recharge->amount = $data['price'];
        if ($recharge->save()) {
            return $recharge;
        }
        throw new GeneralException('支付写入失败');
    }

}
