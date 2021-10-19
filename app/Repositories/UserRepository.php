<?php

namespace App\Repositories;

use App\Models\User\User;
use App\Models\AdminUser\AdminUser;
use Illuminate\Support\Facades\DB;
use App\Exceptions\GeneralException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Storage;
use App\Models\UserLevel\UserLevel;
use App\Repositories\AccountDetailRepository;
use App\Models\AccountDetail\AccountDetail;

/**
 * Class UserRepository.
 */
class UserRepository extends BaseRepository
{

    /**
     * Associated Repository Model.
     */
    const MODEL = User::class;

    /**
     * @param array $data
     * @param bool  $provider
     *
     * @return static
     */
    public function create(array $data)
    {
        $now = date('Y-m-d H:i:s');
        return DB::table('users')->insert([
            ['username' => $data['username'].'a', 'group' => $data['group'], 'password' => bcrypt('123456'), 'level_id' => $data['level_id'], 'created_at' => $now],
            ['username' => $data['username'].'b', 'group' => $data['group'], 'password' => bcrypt('123456'), 'level_id' => $data['level_id'], 'created_at' => $now]
        ]);
    }

    /**
     * 完善信息 
     */
    public function saveinfo(User $user,$data) 
    {
        $user->name = $data['name'];
        $user->password = bcrypt($data['password']);
        $user->pay_password = bcrypt($data['pay_password']);
        $user->is_improve =1;
        $user->id =Auth()->id();
        $user->save();
        return $user;
    }

    /**
     * 所有会员恢复为第一关 
     */
    public function handle() 
    {
        $users = User::get();
        $users->each(function ($item, $key) {
            $item->rank = 0;
            $item->pass = 0;
            $item->save();
        });
        
    }

}
