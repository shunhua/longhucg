<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>登录</title>
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script> 
</head>
<body>
        <div class="login-form">
            <div class="top-login">
                <span><img src="/home/image/logo.png" alt=""></span>
            </div>
            <h3>{{_config('system')}}</h3>
            <p>{{_config('system_en')}}</p>
            <div class="login-top">   
                <div class="login-ic">
                    <input type="text" name="username" placeholder="用户名"/>
                    <input type="password" onfocus="this.type='password'" autocomplete="off" name="password" placeholder="密码（首次登录，默认密码123456）"/>
                    <a href="{{_config('service')}}">联系客服</a>
                </div>
                <div class="log-bwn">
                    <input type="submit"  value="登录"  >
                </div>
                        
            </div>
        
        </div>
<script>
    var flag =1;
    $('.log-bwn').click(function () {
        var username=$.trim($('input[name="username"]').val());
        var password=$.trim($('input[name="password"]').val());
        if(username==''){msg('请输入用户名');return false;}
        if(password==''){msg('请输入密码');return false;}
        if(flag==0)return false;
        flag=0;
        var loading = layer.open({type: 2});
        $.ajax({
            url:"/loginauth.html",
            data:{
                username:username,
                password:password
            },
            type:'post',
            dataType:'json',
            success:function (data) {
                layer.close(loading);
                flag=1;
                msg(data.message);
                if(data.result == 'success'){
                    window.setTimeout(function () {
                        location.href='/';
                    },800);
                }
            }
        });
    });
</script> 
</body>
</html>