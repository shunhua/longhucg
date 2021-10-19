<?php

namespace App\Repositories;

use App\Models\Increase\Increase;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use GuzzleHttp\Client;
use App\Models\AccountDetail\AccountDetail;
use App\Repositories\AccountDetailRepository;

/**
 * Class IncreaseRepository.
 */
class IncreaseRepository extends BaseRepository
{
    /**
     * @var AccountDetailRepository
     */
    protected $accountDetail;
    
    /**
     * RegisterController constructor.
     *
     * @param AccountDetailRepository $accountDetail
     */
    public function __construct(AccountDetailRepository $accountDetail)
    {
        $this->accountDetail = $accountDetail;
    }

    /**
     * Associated Repository Model.
     */
    const MODEL = Increase::class;

    /**
     * @param array $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $increase = self::MODEL;
        $increase = new $increase;
        $increase->user_id = $data['user_id'];
        $increase->amount = $data['amount'];
        $increase->admin_id = $data['admin_id'];

        DB::transaction(function () use ($increase) {
            if ($increase->save()) {
                $param = [
                    'account_type' => AccountDetail::ACCOUNT_INCREASE,
                    'account_amount' => $increase->amount,
                    'relationship_id' => $increase->id,
                    'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_INCREASE,
                    'user_id' => $increase->user_id,
                ];
                $this->accountDetail->create($param);

                return true;
            }
            throw new GeneralException('订单写入失败');
        });
    }

}
