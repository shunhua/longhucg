@extends('layouts.app')

@section('title', '绑卡')

@section('headScript')
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
@endsection

@section('content')
   <div class="home_box center">
            @include('home.user.common',['message'=>'个人中心 — 绑定银行卡'])
            <div class="home_conter">
                <div class='home_msg'>
                    <div class='booking'>
                        <span>绑定银行卡</span>
                        <span class="line"></span>
                    </div>
                </div>
                <div class="home_conter"> 
                    <div class="home_bet cash_mx">
                        <div class="card_bank">
                            <div class="layui-form-item">
                                <label class="layui-form-label">开户银行：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="bank" lay-verify="title" autocomplete="off" placeholder="开户银行" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">银行卡号：</label>
                                <div class="layui-input-block">
                                <input type="text" name="card_no" lay-verify="required" lay-reqtext="ddddddddd" placeholder="银行卡号" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">开户姓名：</label>
                                <div class="layui-input-block">
                                    <input type="text" name="real_name" value="{{Auth::user()->name}}" lay-verify="title" autocomplete="off" placeholder="开户姓名" class="layui-input" readonly>
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">支付密码：</label>
                                <div class="layui-input-block">
                                  <input type="password" name="pay_password" placeholder="支付密码" autocomplete="off" class="layui-input">
                                </div>
                            </div>
                            <div class="form_btn">
                                <button type="button" class="layui-btn layui-btn-normal">绑定</button>
                                <p><b>!</b>银行卡开户人需为账号本人</p>
                            </div>
                        </div>     
                    </div>
                </div>    
            </div>
        </div>
@endsection

@section('footerScript')
<script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
<script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script> 
<script>
var flag =1;
    $('.form_btn').click(function () {
        var bank=$.trim($('input[name="bank"]').val());
        var card_no=$.trim($('input[name="card_no"]').val());
        var real_name=$.trim($('input[name="real_name"]').val());
        var pay_password=$.trim($('input[name="pay_password"]').val());
        if(bank==''){msg('请输入开户银行');return false;}
        if(card_no==''){msg('请输入银行卡号');return false;}
        var myreg=/^[0-9]{16,19}$/;
        if (!myreg.test(card_no)){msg('银行卡号格式不正确');return false;}
        if(real_name==''){msg('请输入开户姓名');return false;}
        if(pay_password==''){msg('请输入支付密码');return false;}
        if(flag==0)return false;
        flag=0;
        var loading = layer.open({type: 2});
        $.ajax({
            url:"/user/savebank.html",
            data:{
                bank:bank,
                card_no:card_no,
                real_name:real_name,
                pay_password:pay_password
            },
            type:'post',
            dataType:'json',
            success:function (data) {
                layer.close(loading);
                flag=1;
                msg(data.message);
                if(data.result == 'success'){
                    window.setTimeout(function () {
                        location.href='/user/withdraw.html';
                    },800);
                }
            }
        });
    });
</script>
@endsection