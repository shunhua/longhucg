<?php

namespace App\Admin\Controllers;

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Layout\Content;
use Illuminate\Routing\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\AdminRole\AdminRole;
use App\Models\AdminUser\AdminUser;
use App\Models\AdminRoleUser\AdminRoleUser;
use Illuminate\Http\Request;
use Encore\Admin\Widgets\Callout;
use Encore\Admin\Layout\Row;
use Encore\Admin\Layout\Column;
use Illuminate\Support\MessageBag;

class AdminUserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.list'));
            $content->body($this->grid()->render());

            $content->row(function (Row $row) {

                $grid = $this->filterGrid();
                $grid->filter($this->filter());
                $conditions = $grid->getFilter()->conditions();

                $userModel = new AdminUser();
                foreach ($conditions as $value) {
                    $userModel = call_user_func_array([$userModel, key($value)], current($value));
                }
                if (! Admin::user()->isAdministrator()) {
                   $userModel = $userModel->whereIn('id', AdminUser::childrenId(Admin::user()->id));
                }

                // 获取当前用户的下级
                $roles = json_decode(Admin::user()->roles, true);
                $roleId = $roles[0]['id'];
                $roleIds = AdminRole::childrenId($roleId);

                $string = '<h4>';

                // 总运营数
                if (in_array(AdminRole::SLUG_OPERATION_ID, $roleIds)) {
                    $copyUserModel = clone $userModel;
                    $operationNum = $copyUserModel->where('role_id', AdminRole::SLUG_OPERATION_ID)->count();
                    $string .= sprintf('总运营: %d &nbsp;&nbsp; ', $operationNum);
                }

                // 总会员单位
                if (in_array(AdminRole::SLUG_MEMBER_ID, $roleIds)) {
                    $copyUserModel = clone $userModel;
                    $memberNum = $copyUserModel->where('role_id', AdminRole::SLUG_MEMBER_ID)->count();
                    $string .= sprintf('总会员: %d &nbsp;&nbsp; ', $memberNum);
                }
                
                // 总区间代理
                if (in_array(AdminRole::SLUG_AGENT_ID, $roleIds)) {
                    $copyUserModel = clone $userModel;
                    $agentNum = $copyUserModel->where('role_id', AdminRole::SLUG_AGENT_ID)->count();
                    $string .= sprintf('总代理: %d &nbsp;&nbsp; ', $agentNum);
                }

                // 总员工
                if (in_array(AdminRole::SLUG_STAFF_ID, $roleIds)) {
                    $copyUserModel = clone $userModel;
                    $agentNum = $copyUserModel->where('role_id', AdminRole::SLUG_STAFF_ID)->count();
                    $string .= sprintf('总员工: %d &nbsp;&nbsp; ', $agentNum);
                }

                $string .= '</h4>';

                $row->column(12, function (Column $column) use ($string) {
                    $column->append((new Callout($string))->style('info'));
                });
            });
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     *
     * @return Content
     */
    public function edit($id)
    {
        if (! Admin::user()->isAdministrator() && ! AdminUser::isChildren(Admin::user()->id, $id)) exit;
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.edit'));
            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        if (! Admin::user()->isAdministrator() && ! AdminUser::isChildren(Admin::user()->id, $id)) exit;
        return $this->form($id)->update($id);
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {
            $content->header(trans('admin.administrator'));
            $content->description(trans('admin.create'));
            $content->body($this->form());
        });
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        if (! Admin::user()->isAdministrator() && ! AdminUser::isChildren(Admin::user()->id, $id)) exit;

        //if (! empty(AdminUser::childrenId($id)) || ! empty(AdminUser::childrenMemberId($id))) exit('此管理存在下级，不能更改');

        if ($this->form()->destroy($id)) {
            return response()->json([
                'status'  => true,
                'message' => trans('admin.delete_succeeded'),
            ]);
        } else {
            return response()->json([
                'status'  => false,
                'message' => trans('admin.delete_failed'),
            ]);
        }
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Administrator::grid(function (Grid $grid) {

            $grid->model()->latest();
            $childrenIds = [];
            if (! Admin::user()->isAdministrator()) {
                $childrenIds = AdminUser::childrenId(Admin::user()->id);
                $grid->model()->whereIn('id', $childrenIds);
            } 

            $grid->id('ID')->sortable();
            $grid->parent_id('上级标识')->display(function ($value) {
                if (Admin::user()->id == $value) return $value;
                if ($value == 0) return '';
                return '<a href="'.admin_url('manage/admin_users?id='.$value).'">'. $value .'</a>';
            });
            $grid->username(trans('admin.username'));
            $grid->name(trans('admin.name'));
            $grid->roles(trans('admin.roles'))->pluck('name')->label();
            $grid->balance('余额')->sortable();
            $grid->deposit('保证金')->sortable();
            $grid->royalty_ratio('返点百分比')->display(function ($value) {
                return "$value".' %';
            });
            $grid->role_id('推广')->urlWrapper();
            
            $grid->code('邀请码');
            $grid->created_at(trans('admin.created_at'));

            $grid->actions(function (Grid\Displayers\Actions $actions) use ($childrenIds) {
                $actions->disableView();
                $key = $actions->getKey();
                // 财务、文章角色
                if ($actions->row->role_id == 7 || $actions->row->role_id == 8) {
                    $actions->disableDelete();
                } 

                if ($key == 1) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                } else if (! empty(AdminUser::childrenId($key)) || ! empty(AdminUser::childrenMemberId($key))) {
                    $actions->disableDelete();
                }
            });
            $grid->disableRowSelector();
            $grid->disableExport();
            // $grid->disableCreation();

            $grid->filter($this->filter());

        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function filterGrid()
    {
        return Admin::grid(Administrator::class, function (Grid $grid) {

        });
    }

    /**
     * 条件
     *
     * @return Closure
     */
    protected function filter()
    {
        return function($filter) {
            $filter->like('name', '代理名称');
            $roles = json_decode(Admin::user()->roles, true);
            $roleId = $roles[0]['id'];
            $roles = AdminRole::whereIn('id', AdminRole::childrenId($roleId))->pluck('name', 'id');
            $filter->equal('role_id', '代理类型')->select($roles);
            $filter->between('created_at', '添加日期')->datetime([
                'format' => 'YYYY-MM-DD HH:mm',
            ]);
        };
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    public function form($id = 0)
    {
        return Administrator::form(function (Form $form) use ($id) {
            $form->display('id', 'ID');
            $userNameRules = 'required';
            if (! $id) $userNameRules .= '|unique:admin_users';
            $form->text('username', trans('admin.username'))->rules($userNameRules);
            $form->text('name', trans('admin.name'))->rules('required');
            $form->image('avatar', trans('admin.avatar'));
            $form->password('password', trans('admin.password'))->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });    
            $actionName=explode('@', \Route::currentRouteAction())[1];   
            if ( $actionName == 'create' ) {
                $form->text('code', '邀请码')->default(generatePassword());    
            } else{
                $form->display('code', '邀请码');
            }     
            $form->ignore(['password_confirmation']);

            $roles = json_decode(Admin::user()->roles, true);

            $firstRole = head($roles);
            // 当前用户的角色
            $roleId = $firstRole['id'];

            if (Admin::user()->isAdministrator()) {
                $roleIds = [];
                $options = Role::orderBy('id', 'asc')->where('id', '<', 7)->pluck('name', 'id');
            } else {
                $roleIds = AdminRole::childrenId($roleId);
                $options = Role::whereIn('id', $roleIds)->orderBy('id', 'asc')->pluck('name', 'id');
            }

            $url =  admin_url('api/user/' . $id);
            $form->select('role_id', trans('admin.roles'))->options($options)->rules('required')->load('parent_id', $url);
            $form->select('parent_id', '上级')->options(function ($parent_id) {
                return AdminUser::where('id', $parent_id)->pluck('name', 'id');
            }); 

            
            if (Admin::user()->isAdministrator()) {
                $form->currency('deposit', '保证金');
            }
            $form->rate('royalty_ratio', '返点百分比')->rules('required|integer|min:0|max:100', [
                'integer'   => '返点百分比必须为整数',
                'min'   => '返点百分比不能小于0',
                'max' => '返点百分比不能大于100',
            ]);

            $form->display('created_at', trans('admin.created_at'));
            $form->display('updated_at', trans('admin.updated_at'));

            $form->saving(function (Form $form) use ($roleIds, $roleId) {
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }

                // 判断添加的角色是否自己的下级
                if (! Admin::user()->isAdministrator()) {
                    if (! in_array($form->role_id, $roleIds)) $error = '非法请求';
                }

                // 判断上级
                if ($form->parent_id) {

                    // 判断用户是否是当前的用户的下级
                    if (Admin::user()->id != $form->parent_id && ! AdminUser::isChildren(Admin::user()->id, $form->parent_id)) $error = '不是用户下级';

                    // 判断用户是否是指定的角色上级
                    // $role_id = AdminUser::find($form->parent_id)->role_id;
                    // $diff = AdminRole::diffeeGrade($role_id, $form->role_id);
                    // if ($diff != 1) $error = '不是指定的上级';

                    // 判断不能相同
                    if ($form->model()->id == $form->parent_id) $error = '不能相同';

                } else {
                    $diff = AdminRole::diffeeGrade($roleId, $form->role_id);
                    // 判断如果是空 同级平台
                    if ($diff === false) {
                        if (! Admin::user()->isAdministrator()) {
                            $error = '不能同级';
                        } else {
                            $form->parent_id = 0;
                        }

                    // 判断相差如果是两级必填上级
                    } elseif ($diff > 1) {
                        $error = '必填上级';

                    // 判断不是两级上级
                    } else {
                        $form->parent_id = Admin::user()->id;
                    }
                }

                // 判断是否可以更改
                $modelRoleId = $form->model()->role_id;
                $modelParentId = $form->model()->parent_id;
                if (! empty($modelRoleId) && $modelRoleId != $form->role_id || ! empty($modelParentId) && $modelParentId != $form->parent_id) {
                    if (! empty(AdminUser::childrenId($form->model()->id)) || ! empty(AdminUser::childrenMemberId($form->model()->id))) {
                        $error = '此管理存在下级，不能更改';
                    }
                }
                // 上级会员代理比例
                $parent_admin_royalty_ratio=AdminUser::adminInfo($form->parent_id);

                if ($form->royalty_ratio > $parent_admin_royalty_ratio) {
                   $error = '不能超过上级管理的代理提成比例: ['.$parent_admin_royalty_ratio.'%]';
                } 
                
                 //下级最大比例 
                $sub_max_royalty_ratio=AdminUser::childrenMaxratio($form->model()->id);  
                if ($form->royalty_ratio < $sub_max_royalty_ratio) {
                   $error = '不能低于下级管理的提成比例: ['.$sub_max_royalty_ratio.'%]';
                }    


                if (isset($error)) {
                    $error = new MessageBag([
                        'title'   => '提示',
                        'message' => $error,
                    ]);
                    return back()->with(compact('error'));
                }

            });

            //保存后回调
            $form->saved(function (Form $form) {
                // 设置代理关系
                \DB::transaction(function () use ($form) {
                    $newRoleId = $form->model()->role_id;
                    $admin_id = $form->model()->id;
                    AdminRoleUser::where('user_id', $admin_id)->delete();
                    AdminRoleUser::create(['role_id' => $newRoleId, 'user_id'=> $admin_id]);
                });
            });

        });
    }

    /**
     * 用户
     */
    public function parent($id, Request $request)
    {
        $role_id = $request->get('q');
        $parent_id = AdminRole::find($role_id)->parent_id;
        $ids = AdminUser::childrenId(Admin::user()->id);
        return AdminUser::whereIn('id', $ids)
                        ->where('id', '!=', $id)
                        ->where('role_id', $parent_id)
                        ->orderBy('id', 'asc')
                        ->get(['id', \DB::raw('name as text')]);
    }

}
