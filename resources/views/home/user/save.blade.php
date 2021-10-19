<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>完善信息</title>
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script>
</head>
<body>
        <div class="login-form reg">
            <div class="reg-top">
                <div class="top-login">
                    <span><img src="/home/image/logo.png" alt=""></span>
                </div>
                <div class="reg-text">
                   <h3>{{_config('system')}}</h3>
                   <p>{{_config('system_en')}}</p>
                </div>

            </div>
            <div class="login-top">
                    
                <div class="login-ic">
                    <input type="text" name="name" placeholder="用户姓名"/>
                    <input type="password" name="password" placeholder="登录密码"/>
                    <input type="password" name="password_sure" placeholder="确认登录密码"/>
                    <input type="password" name="pay_password" placeholder="支付密码"/>
                    <input type="password" name="pay_password_sure" placeholder="确认支付密码"/>
                    <span><b class="pay">!</b>修改后不可更改</span>
                </div>
                <div class="log-bwn">
                    <input type="submit"  value="保存"  >
                </div>
            </div>
        </div>
<script>
    var flag =1;
    $('.log-bwn').click(function () {
        var name=$.trim($('input[name="name"]').val());
        var password=$.trim($('input[name="password"]').val());
        var password_sure=$.trim($('input[name="password_sure"]').val());
        var pay_password=$.trim($('input[name="pay_password"]').val());
        var pay_password_sure=$.trim($('input[name="pay_password_sure"]').val());
        if(name==''){msg('请输入用户姓名');return false;}
        if(password==''){msg('请输入登录密码');return false;}
        if(password_sure==''){msg('请输入确认登录密码');return false;}
        if(pay_password==''){msg('请输入支付密码');return false;}
        if(pay_password_sure==''){msg('请输入确认支付密码');return false;}
        if(password!=password_sure){msg('登录密码不一致');return false;}
        if(pay_password!=pay_password_sure){msg('支付密码不一致');return false;}
        if(flag==0)return false;
        flag=0;
        var loading = layer.open({type: 2});
        $.ajax({
            url:"/saveinfo",
            data:{
                name:name,
                password:password,
                pay_password:pay_password
            },
            type:'post',
            dataType:'json',
            success:function (data) {
                layer.close(loading);
                flag=1;
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