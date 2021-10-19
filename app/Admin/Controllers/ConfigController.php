<?php

namespace App\Admin\Controllers;

use Validator;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use App\Models\Config\Config;
use Encore\Admin\Form;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\MessageBag;

class ConfigController extends Controller
{
    use ModelForm;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('系统配置');
            $content->description(' ');

            $config = Config::where('hide', 0)->orderBy('sort', 'asc')->get();
            foreach ($config as $key => $value) {
                $data[$value['calls']] = $value['value'];
            }

            $form = new \Encore\Admin\Widgets\Form($data);
            $form->action(admin_url('manage/config'));

            foreach ($config as $value) {
                if ($value['type'] == 'text') {
                    $field = $form->text($value['calls'], $value['name']);
                } elseif ($value['type'] == 'switch'){
                    $states = [
                        'on'  => ['value' => 'on', 'text' => '是'],
                        'off' => ['value' => 'off', 'text' => '否'],
                    ];
                    $field = $form->switch($value['calls'], $value['name'])->states($states);
                } elseif ($value['type'] == 'rate') {
                    $field = $form->rate($value['calls'], $value['name']);
                } elseif ($value['type'] == 'currency') {
                    $field = $form->currency($value['calls'], $value['name'])->symbol('￥');
                } elseif ($value['type'] == 'textarea') {
                    $field = $form->textarea($value['calls'], $value['name'])->rows(6);
                } elseif ($value['type'] == 'number') {
                    $field = $form->number($value['calls'], $value['name']);
                } elseif ($value['type'] == 'slider') {
                    $field = $form->slider($value['calls'], $value['name'])->options(['max' => 100, 'min' => 0, 'step' => 1, 'postfix' => ' %']);
                } elseif ($value['type'] == 'editor') {
                    $field = $form->editor($value['calls'], $value['name']);
                } elseif ($value['type'] == 'url') {
                    $field = $form->url($value['calls'], $value['name']);
                }

                if ($value['info'] != NULL) {
                    $field->help($value['info']);
                }
            }
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

        $config = Config::where('hide', 0)->get();
        foreach ($config as $value) {
            $rules[$value['calls']] = $value['validator'];
        }

        $validator = Validator::make($data, $rules);
        if ($validator->fails()) {
            return back()->withInput()->withErrors($validator);
        }

        foreach ($data as $key => $value) {
            Config::where('calls', $key)->update(['value' => $value]);
        }

        $success = new MessageBag([
            'title'   => '成功',
            'message' => '修改已完成',
        ]);
        return redirect()->guest(admin_url('manage/config'))->with(compact('success'));
    }



}
