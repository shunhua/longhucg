@extends('layouts.app')

@section('title', '首页')

@section('headScript')
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
     <div class="content_box">
            <div class="box_1 left">
                <img src="/home/image/shicai.png" alt="" width="60">
            </div>
            <div class="box_2 left">
                <span>第<strong style="color:#faa251"> {{$periods}} </strong>期下注截止：</span >
                <span class="big_time" id="time">00:00:00</span>
            </div>
            <div class="box_3 left">
                @if($end_last)
                    <p>第<strong style="color:#faa251"> {{$end_last->periods}} </strong>期</p>
                    <ul>
                        <li>{{explode(',',$end_last->open_number)[0]}}</li>
                        <li>{{explode(',',$end_last->open_number)[1]}}</li>
                        <li>{{explode(',',$end_last->open_number)[2]}}</li>
                        <li>{{explode(',',$end_last->open_number)[3]}}</li>
                        <li>{{explode(',',$end_last->open_number)[4]}}</li>
                    </ul>
                @else
                    <p>第<strong style="color:#faa251">  </strong>期</p>
                    <ul>
                        <li>-</li>
                        <li>-</li>
                        <li>-</li>
                        <li>-</li>
                        <li>-</li>
                    </ul>
                @endif
            </div>
            <div class="box_4 left">
               <a href="<?php echo Route('trend')?>">号码走势</a>
               <a href="<?php echo Route('guide')?>" style="margin-top:5px">新手指导</a>
            </div>
        </div>

        <div class="content_main">
            <div class="main_top am-form-group">
                <ul class="ui-choose am-form-group" id="nav">
                    @foreach ($orderType as $k=>$v)
                        <li class=" @if($k == $locktype) num @endif">{{$v}}</li>
                    @endforeach  
                </ul>
                <div class="clear"></div>
                <p>游戏玩法开奖结果万位大于干位为龙、千位大于万位为虎、二者相同为和(开“和”系统自动撤单)</p>
            </div>
            <div class="main_center">
                <div class="main_l left">
                    <div class="main_s left"><img src="/home/image/long.png" alt=""><i class="div_1">龙</i></div>
                    <p class="left"><img src="/home/image/vs.png" alt=""></p>
                    <div class="main_s left"><img src="/home/image/hu.png" alt=""><i class="div_1">虎</i></div>
                </div>
                <div class="main_r right">
                    <h1 style="font-size:46px;">{{$rank->name}}</h1>
                    <input type="hidden" value="{{$rank->id}}" name="barrier">
                    @if($rank->id == 1)
                        <!-- 第一关 -->
                        <button style="margin-right: 30px;" class="smallBtn layui-btn" onclick="selSub(<?php echo $rank->price_1?>)">下注{{$rank->price_1}}</button>
                        <button class="bigBtn layui-btn" onclick="selSub(<?php echo $rank->price_2?>)">下注{{$rank->price_2}}</button>
                    @else
                        <!-- 其他关 -->
                        @if($user->rank == 1)
                            <button class="bigBtn layui-btn" onclick="order(<?php echo $rank->price_1?>)">下注{{$rank->price_1}}</button>
                        @elseif($user->rank == 2)
                            <button class="bigBtn layui-btn" onclick="order(<?php echo $rank->price_2?>)">下注{{$rank->price_2}}</button>
                        @endif 
                    @endif
                </div>
            </div>
            <!-- ... -->
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
                                @endif  
                            </tbody>
                        </table>
            
                    </div>
            <!-- ... -->
        </div>
        <div class="content_right">
            <p>最近30期开奖结果</p>
            <div class="srcollbar">
                <table class="layui-table" lay-size="sm">
                    <colgroup>
                        <col width="70">
                        <col width="70">
                        <col width="70">
                    </colgroup>
                    <thead>
                        <tr>
                        <th>期号</th>
                        <th>开奖号码</th>
                        <th><span id="numShow">万千</span></th>
                        </tr> 
                    </thead>
                    <tbody>
                        @if (!$lotterys->isEmpty())
                        @foreach ($lotterys as $lottery)
                            <tr>
                                <td><span class="issue">{{substr($lottery->periods,'-7')}}</span></td>
                                <td>
                                    <span class="number one">
                                        <i>{{explode(',',$lottery->open_number)[0]}}</i>
                                        <i>{{explode(',',$lottery->open_number)[1]}}</i>
                                        <i>{{explode(',',$lottery->open_number)[2]}}</i>
                                        <i>{{explode(',',$lottery->open_number)[3]}}</i>
                                        <i>{{explode(',',$lottery->open_number)[4]}}</i>
                                    </span>
                                </td>
                                <td><span class="type"></span></td>
                            </tr>
                        @endforeach  
                        @endif
                    </tbody>
                </table>

            </div>
            
        </div>

@endsection

@section('footerScript')
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script> 
    <script>
         $(function () {
            //龙虎点击
            $('.main_s').mouseenter(function(){
                $(this).find('i').addClass('showme');
            });
            $('.div_1').bind('click', function(){
                $('.div_1').removeClass('intro');
                $('.div_1').removeClass('showme');
                $(this).addClass('intro');
            });
        })
        //万、千、百选择
        window.onload = function(){
            let nav=document.getElementById('nav');
            let oNav=nav.getElementsByTagName('li');
            let number=document.getElementsByClassName('number');
            let numshow=document.getElementById('numShow');
            let type = document.getElementsByClassName('type');
            for(var j =0;j<number.length;j++){
           
                let numChild = number[j].children;
                for(var i=0;i<type.length;i++){
                type[j].className = 'type'
                }
                if(numChild[0].innerText > numChild[1].innerText){
                type[j].classList.add('one');
                type[j].innerText = '龙'
                }else if(numChild[0].innerText < numChild[1].innerText){
                    type[j].classList.add('two');
                    type[j].innerText = '虎'
                }else{
                    type[j].classList.add('three');
                    type[j].innerText = '和'
                }
            }
            for(var i=0;i<oNav.length;i++){
                oNav[i].index=i;
            
                oNav[i].onclick=function () { 
                var text = this.innerText;
                numshow.innerText=text;
                for(var i=0;i<oNav.length;i++){
                    oNav[i].className='';
                }
                
                for(var j =0;j<number.length;j++){
                    number[j].className = 'number'
                    let numChild = number[j].children;
                    for(var i=0;i<type.length;i++){
                    type[j].className = 'type'
                    }
                    if(this.index === 1){
                    number[j].classList.add('two');
                    if(numChild[0].innerText > numChild[2].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                    }else if(numChild[0].innerText < numChild[2].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                    }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                    }
                    }else if(this.index === 2){
                        number[j].classList.add('three')
                        if(numChild[0].innerText > numChild[3].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[0].innerText < numChild[3].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 3){
                        number[j].classList.add('four')
                        if(numChild[0].innerText > numChild[4].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[0].innerText < numChild[4].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 4){
                        number[j].classList.add('five')
                        if(numChild[1].innerText > numChild[2].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[1].innerText < numChild[2].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 5){
                        number[j].classList.add('six')
                        if(numChild[1].innerText > numChild[3].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[1].innerText < numChild[3].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 6){
                        number[j].classList.add('seven')
                        if(numChild[1].innerText > numChild[4].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[1].innerText < numChild[4].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 7){
                        number[j].classList.add('eight')
                        if(numChild[2].innerText > numChild[3].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[2].innerText < numChild[3].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 8){
                        number[j].classList.add('nine')
                        if(numChild[2].innerText > numChild[4].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[2].innerText < numChild[4].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else if(this.index === 9){
                        number[j].classList.add('ten')
                        if(numChild[3].innerText > numChild[4].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[3].innerText < numChild[4].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }else{
                        number[j].classList.add('one')
                        if(numChild[0].innerText > numChild[1].innerText){
                        type[j].classList.add('one');
                        type[j].innerText = '龙'
                        }else if(numChild[0].innerText < numChild[1].innerText){
                        type[j].classList.add('two');
                        type[j].innerText = '虎'
                        }else{
                        type[j].classList.add('three');
                        type[j].innerText = '和'
                        }
                    }
                }
                this.className = 'num'
                }
            }
        }


        //倒计时
    // Set the date we're counting down to
    var countDownDate = new Date("<?php echo $end_time ?>").getTime();
        // Update the count down every 1 second
    var x = setInterval(function() {
        // Get todays date and time
        var now = new Date().getTime();
        // Find the distance between now an the count down date
        var distance = countDownDate - now;
        var hours = Math.floor(distance/1000/60/60%24);  
        // Time calculations for days, hours, minutes and seconds
        var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        var seconds = Math.floor((distance % (1000 * 60)) / 1000);
        if (minutes <= 9) minutes = '0' + minutes;
        if (seconds <= 9) seconds = '0' + seconds;
        if (hours <= 9) hours = '0' + hours;
        // Display the result in the element with id="demo"
        var countdown = hours+":"+minutes+":"+seconds+"";
        if (distance < 0) {
            document.getElementById("time").innerHTML = "开奖中...";
        }else{
            document.getElementById("time").innerHTML = countdown;
        }
        // If the count down is finished, write some text 
        if (distance < -4500) {
            clearInterval(x);
            document.getElementById("time").innerHTML = "开奖中...";
             // 显示
            setTimeout(
            $.ajax({
                type : 'POST',
                url : '/statistics',
                data : 'id=' +<?php echo $lottery_id ?>,
                dataType : 'JSON',
                async : false,
                success : function(data) {
                    if (data.result == 'success') {
                        msg_bsh_jump(data.data.msg,'/');
                    }
                },
            }),1000);
        }
    }, 1000); 

    // 第一关投注选择
    var flag=1;
    function selSub(price) {
        //询问框
        layer.open({
            content: '是否下注'+price+'元挡？'
            ,btn: ['确定', '取消']
            ,yes: function(index){
                if(flag==0)return false;
                flag=0;
                var loading = layer.open({type: 2});
                layer.close(loading);
                flag=1;
                layer.close(index);
                order(price);
            }
        });
    }
    // 下单
    function order(price) {
        var isClick = true;
        if(isClick) {
            isClick = false;
            var countDownDate = new Date("<?php echo $end_time ?>").getTime();
            var now = new Date().getTime(); 
            var distance = (countDownDate - now) / 1000;
            if(distance <= 300){
                msg_bsh_jump("开奖5分钟内暂不能投注");return false;
            }
            var type = $(".num").html();
            var lh_type = $(".intro").html();
            var barrier=$.trim($('input[name="barrier"]').val());
            if ( typeof type == 'undefined'){
                msg_bsh_jump('未选择下注类型');return false;
            } 
            if ( typeof lh_type == 'undefined'){
                msg_bsh_jump('未选择竞猜结果[龙/虎]');return false;
            } 
            if (barrier==''){
                msg_bsh_jump('参数错误');return false;
            } 
            $.ajax({
                url:"/order/add.html",
                data:{
                    type:type,
                    lh_type:lh_type,
                    barrier:barrier,
                    amount:price,
                    lottery_id:<?php echo $lottery_id ?>
                },
                type:'post',
                dataType:'json',
                success:function (data) {
                    msg_bsh_jump(data.message);
                    if(data.result == 'success'){
                        msg_bsh_jump('已下注,等待开奖','/');
                    }
                }
            });
            
            //定时器
            setTimeout(function() {
                isClick = true;
            }, 1000);//一秒内不能重复点击
        }
        
        
    }

        
    </script>
@endsection