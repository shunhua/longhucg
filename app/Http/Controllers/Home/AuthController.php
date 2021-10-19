<?php

namespace App\Http\Controllers\Home;

use Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\User\User;
use App\Models\AdminUser\AdminUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\Repositories\UserRepository;


class AuthController extends Controller
{
    /**
     * @var UserRepository, SmsRepository
     * @author bsh
     */
    protected $user;
    
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    } 

    public function login(){
        return view('home.auth.login');
    }

    /**
     * 登录认证
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function loginAuth(Request $request){
        $param = $request->only('username','password');
        $username = User::where('username', $param['username'])->exists();
        if (!$username) {
            return responseJson('此用户不存在');
        }
        $freeze = User::where('username', $param['username'])->value('freeze');
        if ($freeze) {
            return responseJson('此用户已冻结');
        }
        if (Auth::attempt(['username'=>$param['username'],'password'=>$param['password'],])){
            User::where('username', $param['username'])->update(['last_login_at' => Carbon::now(),'last_ip' => $request->getClientIp()]);
            return responseJson('登录成功', true);
        }else{
            return responseJson('用户名或密码不正确');
        }
    }

    /**
     * 保存会员 
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function saveUser(Request $request){
        $param = $request->only('mobile', 'sms', 'password', 'invite');
        $user = $this->user->create($param);
        event(new Registered($user));
        return responseJson('注册成功', true);
    }

    /**
     * 退出
     *
     * @return void
    */
    public function logout(){
        Auth::logout();
        return redirect()->route('login');
    }

}
