<?php

namespace App\Admin\Controllers;

use Validator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Form;
use App\Models\UserLevel\UserLevel;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;
use App\Repositories\UserRepository;

class CreateUserController extends Controller
{
    use ModelForm;

    protected $user;
    
    /**
     * RegisterController constructor.
     *
     * @param AccountDetailRepository $accountDetail
     */
    public function __construct(UserRepository $user)
    {
        $this->user = $user;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('新增会员');
            $content->description(' ');

            $content->row((new Box('新增提示', '<p>1. 默认生成密码为123456</p><p>2. 同组会员名规则为用户名+[a/b]</p>'))->style('warning'));

            $form = new \Encore\Admin\Widgets\Form();
            $form->action(admin_url('manage/create_user'));
            $task = UserLevel::pluck('name', 'id')->toArray();
            $form->select('level_id', '用户级别')->options($task)->rules('required');
            $form->text('username', '用户名称')->rules('required')->help('用户名唯一');
            $form->text('group', '用户组名')->rules('required')->help('用户组名唯一');

            $form->hidden('_token')->default(csrf_token());

            $content->row(new Box(' ', $form));
        });
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store()
    {
        $data = Input::all();
        $flag = $this->user->create($data);
        if ($flag) {
           $success = new MessageBag([
               'title'   => '成功',
               'message' => '新增完成',
           ]);
           return redirect()->guest(admin_url('manage/create_user'))->with(compact('success'));
        }else{
            $error = new MessageBag([
                'title'   => '提示',
                'message' => '新增失败',
            ]);
            return back()->with(compact('error'));
        }
        
    }



}
