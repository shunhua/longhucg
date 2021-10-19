@extends('layouts.app')

@section('title', '提现')

@section('headScript')
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script> 
@endsection

@section('content')
   <div class="home_box center">
            @include('home.user.common',['message'=>'个人中心 — 提现'])
            <div class="home_conter">
                <div class='home_msg'>
                    <div class='booking'>
                        <span>我的账户</span>
                        <span class="line"></span>
                    </div>
                    <div class="msg_bar">
                        <ul>
                            <li>用户名:
                                <span>{{Auth::user()->username}}</span>
                            </li>
                            <li>真实姓名:
                                <span>{{Auth::user()->name}}</span>
                            </li>
                            <li>账户余额:
                                <span style="color:#faa251;font-weight: bold;">{{Auth::user()->balance}}</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="home_yhk">
                    <div class='booking'>
                        <span>我的银行卡</span>
                        <span class="line"></span>
                    </div>
                    
                </div>
                <div class="home_bet cash_mx">
                   
                    <div class="layui-form-item">
                        <label class="layui-form-label">可提现金额：</label>
                        <div class="layui-input-block">
                            <p class="balance">{{Auth::user()->balance}}</p>
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现到卡：</label>
                        <div class="layui-input-block">
                            @if ($card)
                                <input type="hidden" name="card_id" value="{{$card->id}}">
                                <p style="color:#6e6e6e;">{{ formatBankCardNo($card->card_no) }}<span>({{ $card->bank }})</span></p>
                            @else
                                <input type="hidden" name="card_id" value="">
                                <p><a href="<?php echo Route('user/bindcard')?>"><span style="color:red;">请先去绑定银行卡</span></a></p>
                            @endif 
                        </div>
                    </div>
                    <div class="layui-form-item">
                        <label class="layui-form-label">提现金额：</label>
                        <div class="layui-input-block">
                        <input type="text" name="money" lay-verify="required" lay-reqtext="ddddddddd" placeholder="提现金额" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    
                    <div class="layui-form-item">
                        <label class="layui-form-label">支付密码：</label>
                        <div class="layui-input-block">
                        <input type="password" name="pay_password" placeholder="支付密码" autocomplete="off" class="layui-input">
                        </div>
                    </div>
                    <div class="form_btn">
                        <button type="button" class="layui-btn layui-btn-normal">确定提现</button>
                        <p><b>!</b>提现手续费为提现数额的{{_config('atmPoundage')}}%</p>
                    </div>
                </div>
            </div> 
        </div>
@endsection

@section('footerScript')
<script>
    var flag =1;
    $('.form_btn').click(function () {
        var card_id=$.trim($('input[name="card_id"]').val());
        var money=$.trim($('input[name="money"]').val());
        var pay_password=$.trim($('input[name="pay_password"]').val());
        if(card_id==''){msg('请先去绑定银行卡');return false;}
        if(money==''){msg('请输入提现金额');return false;}
        if(pay_password==''){msg('请输入支付密码');return false;}
        if(flag==0)return false;
        flag=0;
        var loading = layer.open({type: 2});
        $.ajax({
            url:"/atm/sub.html",
            data:{
                card_id:card_id,
                pay_password:pay_password,
                price:money
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