@extends('layouts.app')

@section('title', '充值')

@section('headScript')
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
   <div class="home_box center">
            @include('home.user.common',['message'=>'个人中心 — 充值'])
            <div class="home_conter regch">
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
                                <a href="<?php echo Route('user/pay')?>" class="msg_cash">充值</a>
                            </li>
                            
                        </ul>
                    </div>
                </div>
                <div class="home_bet">
                    <div class='booking'>
                        <span>充值明细</span>
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
                                        <td><span class="add">+{{ $account->account_amount }}元</span></td>
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