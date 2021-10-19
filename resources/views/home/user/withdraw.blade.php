@extends('layouts.app')

@section('title', '提现明细')

@section('headScript')
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
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
                                <a href="<?php echo Route('user/atm')?>" class="msg_cash">提现</a>
                            </li>
                            
                        </ul>
                    </div>

                </div>
                <div class="home_yhk">
                    <div class='booking'>
                        <span>我的银行卡</span>
                        <span class="line"></span>
                    </div>
                    <div class="msg_bar yhk">
                        @if ($card)
                            <span>{{ $card->bank }}：<span style="color:#6e6e6e;">{{ formatBankCardNo($card->card_no) }}</span></span>
                            <a href="javascript:;" onclick="del(<?php echo $card->id?>,this)">解绑</a>
                        @else
                            <a href="<?php echo Route('user/bindcard')?>">绑卡</a>
                        @endif 
                    </div>
                </div>
                <div class="home_bet">
                    <div class='booking'>
                        <span>提现明细</span>
                        <span class="line"></span>
                    </div>
                    <div class="main_table">
                        <table class="layui-table">
                            <colgroup>
                                <col width="155">
                                <col width="155">
                                <col width="155">
                                <col width="155">
                                <col width="155">
                            </colgroup>
                            <thead>
                                <tr>
                                <th>时间</th>
                                <th>用途</th>
                                <th>详情</th>
                                <th>金额</th>
                                <th>余额</th>
                                </tr> 
                            </thead>
                            <tbody>
                                @if (!$accounts->isEmpty())
                                    @foreach ($accounts as $account)
                                        <tr>
                                            <td><span>{{ $account->created_at->format('Y.m.d H:i') }}</span></td>
                                            <td><span>{{ _type($account->relationship_type) }}</span></td>
                                            <td><span>{{ _type($account->account_type, 2) }}</span></td>
                                            <td><span class="reduce">-{{ $account->account_amount }}元</span></td>
                                            <td><span class="balance">{{ $account->balance }}</span></td>
                                        </tr>
                                    @endforeach  
                                @else
                                    <div class="notable">
                                        <p>暂无数据</p>
                                    </div>
                                @endif  
                            </tbody>
                            
                        </table>
                        <!-- 分页 -->
                        {!! $accounts->links() !!}
            
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
        var flag=1;
        function del(id,obj) {
            //询问框
            layer.open({
                content: '您确定要解绑银行卡？'
                ,btn: ['确定', '取消']
                ,yes: function(index){
                    if(flag==0)return false;
                    flag=0;
                    var loading = layer.open({type: 2});
                    $.ajax({
                        url:"/card/remove.html",
                        data:{
                            id:id
                        },
                        type:'post',
                        dataType:'json',
                        success:function (data) {
                            layer.close(loading);
                            flag=1;
                            if(data.result == 'success'){
                                msg_jump(data.message,"/user/withdraw.html")
                            }
                        }
                    });
                    layer.close(index);
                }
            });
        }  
    </script>
@endsection