@extends('layouts.app')

@section('title', '支付')

@section('headScript')
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
     <div class="home_box center">
            <div class="home_bar">
                <span>个人中心 — 充值</span>
                <ul class="right">
                    <li><a href="<?php echo Route('user/account')?>">资金明细</a></li>
                    <li><a href="<?php echo Route('user/recharge')?>">充值</a></li>
                    <li><a href="<?php echo Route('user/withdraw')?>">提现</a></li>
                </ul>
            </div>
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
                        <span>充值</span>
                        <span class="line"></span>
                    </div>
                    
                </div>
                <div class="home_bet cash_mx rech">
                    <div class="layui-form">
                        <div class="layui-form-item">
                            <label class="layui-form-label">充值单号：</label>
                            <div class="layui-input-block">
                                <p style="color:#6e6e6e;" id="trade_no">{{trade_no()}}</span></p>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">充值金额：</label>
                            <div class="layui-input-block">
                            <input type="text" name="price" lay-verify="required" lay-reqtext="ddddddddd" placeholder="充值金额" autocomplete="off" class="layui-input">
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <div class="layui-input-block">
                                <div class="layui-unselect layui-form-radio layui-form-radioed">
                                    <i class="layui-anim layui-icon"></i><div>支付完成后请主动联系客服完成充值，否则未及时到账，本公司不负任何责任！</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="code_qr">
                        <div class="left">
                            <img src="{{_ad('alipay')}}" alt="">
                            <p>支付宝</p>
                        </div>
                        <div class="left">
                            <img src="{{_ad('wechat')}}" alt="">
                            <p>微信</p>
                        </div>
                    </div>
                    <div class="code_btn">
                        <button type="button" class="layui-btn layui-btn-normal submit_btn">我已支付</button><br>
                        <a href="{{_config('service')}}" class="layui-btn layui-btn-normal" style=" background-color: #2d2f33;">联系客服</a>
                    </div>
                </div>
            </div>
        </div>
@endsection

@section('footerScript')
<script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
<script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
<script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script>  
<script>
var flag =1;
$('.submit_btn').click(function () {
    var trade_no=document.getElementById("trade_no").innerHTML;
    var price=$.trim($('input[name="price"]').val());
    if(trade_no==''){msg('充值单号');return false;}
    if(price==''){msg('请输入充值金额');return false;}
    if(flag==0)return false;
    flag=0;
    var loading = layer.open({type: 2});
    $.ajax({
        url:"/pay/sub.html",
        data:{
            trade_no:trade_no,
            price:price
        },
        type:'post',
        dataType:'json',
        success:function (data) {
            layer.close(loading);
            flag=1;
            msg(data.message);
            if(data.result == 'success'){
                window.setTimeout(function () {
                    location.href='/user/recharge.html';
                },800);
            }
        }
    });
});
</script>
@endsection