<?php

namespace App\Repositories;

use App\Models\Atm\Atm;
use App\Models\Card\Card;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use App\Models\AccountDetail\AccountDetail;
use App\Repositories\AccountDetailRepository;

/**
 * Class AtmRepository.
 */
class AtmRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Atm::class;

    /**
     * @var AccountDetailRepository
     */
    protected $accountDetail;
        
    /**
     * AtmController constructor.
     *
     * @param AccountDetailRepository $accountDetail
     */
    public function __construct(AccountDetailRepository $accountDetail) 
    {
        $this->accountDetail = $accountDetail;
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public function create(array $data)
    {
        $cards=Card::where('id',$data['card_id'])->first();
        $user = auth()->user();
        $atm = self::MODEL;
        $atm = new $atm;
        $atm->user_id = $user->id;
        $atm->parent_admin = $user->parent_admin;
        $atm->analog = $user->analog;
        $atm->price = $data['price'];
        $atm->real_amount = $data['price'] - ($data['price'] * (_config('atmPoundage')/100));
        $atm->name = $cards->real_name;
        $atm->bank = $cards->bank;
        $atm->card = $cards->card_no;
        $atm->state = Atm::STATE_NORMAL;
        $rec_count = Atm::where('user_id', $atm->user_id)
                        ->where('created_at', '>=', date('Y-m-d'))
                        ->count();               
        // if ($rec_count > 0) throw new GeneralException('您今日已申请过,暂不能申请');
        //if($atm->price < _config('min_atm')) throw new GeneralException('最低提现额度为'._config('min_atm'));
        if($atm->price > $user->balance) throw new GeneralException('余额不足');
        if(!password_verify($data['pay_password'],$user->pay_password)) throw new GeneralException('支付密码不正确');
        // if($atm->price%100 != 0) throw new GeneralException('提现金额规定为100的整数倍');
        DB::transaction(function () use ($atm, $user) {
            if ($atm->save()) {
                // 写入流水
                $param = [
                    'account_type' => AccountDetail::ACCOUNT_DECREASE,
                    'account_amount' => $atm->price,
                    'relationship_id' => $atm->id,
                    'relationship_type' => AccountDetail::RELATIONSHIP_TYPE_ATM,
                    'parent_admin' => $atm->parent_admin,
                    'analog' => $atm->analog
                ];
                $this->accountDetail->create($param);
                return true;
            }
            return $atm->id;
            throw new GeneralException('提现申请失败');
        });
    }

}
