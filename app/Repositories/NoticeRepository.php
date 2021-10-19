<?php

namespace App\Repositories;

use App\Models\Notice\Notice;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Storage;

/**
 * Class NoticeRepository.
 */
class NoticeRepository extends BaseRepository
{
    /**
     * Associated Repository Model.
     */
    const MODEL = Notice::class;

    public function create($user_id, $status)
    {
        $notice = self::MODEL;
        $notice = new $notice;
        $notice->user_id = $user_id;
        $notice->title = $status==1 ? '认证成功' : '认证失败';
        $notice->sub_title = $status==1 ? '认证成功' :'认证失败';
        $notice->contents = $status==1 ? '您的实名认证审核成功' :'您的实名认证审核失败，失败原因：填写信息与照片不匹配（请重新提交认证）';
        $notice->status = 0;
        if ($notice->save()) return true;
        throw new GeneralException('写入失败');
    }

}
