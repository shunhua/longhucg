<?php

namespace App\Repositories;

use App\Models\AccountDetail\AccountDetail;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Repositories\UserRepository;

/**
 * Class AccountDetailRepository.
 */
class AccountDetailRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = AccountDetail::class;

        /**
     * @var UserRepository
     */
    protected $user;
    
    /**
     * RegisterController constructor.
     *
     * @param ConfigMarketRepository $configMarket
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $data
     *
     * @return stati
     */
    public function create(array $data)
    {
        //$user = auth()->user() ?: $this->user->find($data['user_id']);
        $user = isset($data['user_id']) ? $this->user->find($data['user_id']) : auth()->user();
        $accountDetail = self::MODEL;
        $accountDetail = new $accountDetail;
        $accountDetail->user_id = $user->id;
        $accountDetail->parent_admin = isset($data['parent_admin']) ? $data['parent_admin'] : $user->parent_admin;
        $accountDetail->analog = isset($data['analog']) ? $data['analog'] : $user->analog;
        $accountDetail->account_type = $data['account_type'];
        $accountDetail->account_amount = $data['account_amount'];
        $accountDetail->relationship_id = $data['relationship_id'];
        $accountDetail->relationship_type = $data['relationship_type'];
        $accountDetail->remark = isset($data['remark']) ? $data['remark'] : AccountDetail::RELATIONSHIP_TYPE[$data['relationship_type']];

        DB::transaction(function () use ($accountDetail, $user) {
            if ($accountDetail->account_type == AccountDetail::ACCOUNT_INCREASE) {
                $user->increment('balance', $accountDetail->account_amount);
            } else {
                $user->decrement('balance', $accountDetail->account_amount);
            }
            $accountDetail->balance = $user->balance;
            if ($accountDetail->save()) return true;
            throw new GeneralException('流水写入失败');
        });
    }

}
