<?php

namespace App\Repositories;

use App\Models\Reduce\Reduce;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use GuzzleHttp\Client;
use App\Models\AccountDetail\AccountDetail;
use App\Repositories\AccountDetailRepository;

/**
 * Class ReduceRepository.
 */
class ReduceRepository extends BaseRepository
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
    const MODEL = Reduce::class;

    /**
     * @param array $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $reduce = self::MODEL;
        $reduce = new $reduce;
        $reduce->user_id = $data['user_id'];
        $reduce->amount = $data['amount'];
        $reduce->admin_id = $data['admin_id'];

        DB::transaction(function () use ($reduce) {
            if ($reduce->save()) {
                $param = [
                    'account_type' => AccountDetail::ACCOUNT_DECREASE,
                    'account_amount' => $reduce->amount,
                    'relationship_id' => $reduce->id,
                    'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_REDUCE,
                    'user_id' => $reduce->user_id,
                ];
                $this->accountDetail->create($param);

                return true;
            }
            throw new GeneralException('订单写入失败');
        });
    }

}
