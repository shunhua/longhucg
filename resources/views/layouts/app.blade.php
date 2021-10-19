<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{_config('system')}} - @yield('title')</title>
    @yield('headScript')
</head>
    <body>
    <div class="warp">
        <div class="m_bar">
            <div class="mod_left">
                <div class="mod_img left">
                    <span><img src="/home/image/logos.png" alt=""></span>
                </div>
                <div class="mod-text left">
                    <h3>{{_config('system')}}</h3>
                    <p>{{_config('system_en')}}</p>
                </div>

            </div>
            <div class="mod_right">
                <div class="mod_sum left">
                    <p>{{Auth::user()->username}}</p>
                    <p>账户余额：<strong style="color:#faa251">{{Auth::user()->balance}}</strong></p>
                </div>
                <div class="mod_out right">
                   <i><a href="<?php echo Route('logout')?>"><img src="/home/image/signout.png" alt="" width="100%"></i></a></i>
                </div>
            </div>
        </div>
        <div class="m_head_menu search">
            <div class="head_nav">
                <ul class="menu_l">
                    <li class="welcome {{ active_class(if_route('/') || if_route('trend') || if_route('guide'),'active') }}"><a href="<?php echo Route('/')?>">首页</a></li>
                    <li class="{{ active_class(if_route('user/index') || if_route('user/account') || if_route('user/recharge') || if_route('user/pay') || if_route('user/withdraw') || if_route('user/atm') || if_route('user/bindcard') ,'active') }}"><a href="<?php echo Route('user/index')?>">个人中心</a></li>
                </ul>
                <ul class="menu_r">
                    <li><a href="{{_config('service')}}">联系客服</a></li>
                    <li class="{{ active_class(if_route('notice'),'new_active') }}"><a href="<?php echo Route('notice')?>">消息中心</a></li>
                    <li><a href="{{_config('download')}}">下载客户端</a></li>
                </ul>
            </div>
        </div>
        @yield('content')
    
    </div>
    </body>
    @yield('footerScript')
    
</html>