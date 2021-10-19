@extends('layouts.app')

@section('title', '个人中心')

@section('headScript')
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
    <div class="home_box center">
            @include('home.user.common',['message'=>'个人中心'])
            <div class="home_conter">
                <div class='home_msg'>
                    <div class='booking'>
                        <span>我的信息</span>
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
                            <li>会员等级:
                                <span style="color:#faa251;">{{Auth::user()->level->name}}</span>
                            </li>
                            <li>上次登录时间:
                                <span>{{Auth::user()->last_login_at}}</span>
                            </li>
                            <li>上次登录IP:
                                <span>{{Auth::user()->last_ip}}</span>
                            </li>
                        </ul>
                    </div>

                </div>
                <div class="home_bet">
                    <div class='booking'>
                        <span>我的下注</span>
                        <span class="line"></span>
                    </div>
                    <div class="main_table">
                        <table class="layui-table">
                            <colgroup>
                                <col width="136">
                                <col width="120">
                                <col width="120">
                                <col width="136">
                                <col width="136">
                                <col width="136">
                                <col width="136">
                                <col width="155">
                            </colgroup>
                            <thead>
                                <tr>
                                <th>投注期号</th>
                                <th>下注类型</th>
                                <th>竞猜单位</th>
                                <th>金额</th>
                                <th>输/赢</th>
                                <th>收益</th>
                                <th>关数</th>
                                <th>下单时间</th>
                                </tr> 
                            </thead>
                            <tbody>
                                @if (!$orders->isEmpty())
                                    @foreach ($orders as $order)
                                        <tr>
                                            <td>{{$order->lottery->periods}}</td>
                                            <td>{{$orderType[$order->type]}}</td>
                                            <td>{{$lhType[$order->lh_type]}}</td>
                                            <td>{{$order->amount}}</td>
                                            @if(!$order->is_win)
                                                <td style="color: red">{{$winType[$order->is_win]}}</td>
                                            @else
                                                <td>{{$winType[$order->is_win]}}</td>
                                            @endif
                                            <td>{{$order->profit}}</td>
                                            <td>{{$barrier[$order->barrier]}}</td>
                                            <td>{{$order->created_at->format('Y.m.d H:i')}}</td>
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
                        {!! $orders->links() !!}
            
                    </div>
                </div>
            </div>
            
        </div>
@endsection