<?php

namespace App\Repositories;

use App\Models\Card\Card;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use GuzzleHttp\Client;

/**
 * Class CardRepository.
 */
class CardRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Card::class;

    /**
     * @param array $data
     *
     * @return static
     */
    public function create($data)
    {
        $exist = Auth()->user()->cards()->exists();
        if ($exist) throw new GeneralException('您已绑定过');
        if(!password_verify($data['pay_password'],auth()->user()->pay_password)) throw new GeneralException('支付密码不正确');
        $card = self::MODEL;
        $card = new $card;
        $card->user_id = Auth()->id();
        $card->real_name = $data['real_name'];
        $card->card_no = $data['card_no'];
        $card->bank = $data['bank'];
        if ($card->save()) {
            return $card->id;
        }
        throw new GeneralException('绑定失败');
    }

}
