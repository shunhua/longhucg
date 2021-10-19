layui.use(['layer','util','table'], function(){
    //弹窗
    var layer = layui.layer;
    var active = {
        notice: function(){
            layer.open({
                title: ' '
                ,content: '是否下注一百元挡？'
                ,area: ['350px', '210px']
                ,id: 'LAY_layuipro' //设定一个id，防止重复弹出
                ,btn: ['取消', '确定']
                ,success: function(layero){
                var btn = layero.find('.layui-layer-btn');
                btn.find('.layui-layer-btn1').attr({
                    href: 'https://www.baidu.com/'
                    ,target: '_blank'
                });
                }
            });
        }
        //其他弹窗
        ,offset:function(){
            layer.open({
                type:1,
                title: ' '
                ,content:$("#test")
                ,area: ['350px', '210px']
                
                
            });
        }
    }
    $('.main_r button').on('click', function(){
        var othis = $(this), method = othis.data('method');
        active[method] ? active[method].call(this, othis) : '';
    });
   
    //倒计时
    var util = layui.util;
    var endTime = new Date(2019,7,14).getTime() //假设为结束日期
    ,serverTime = new Date(); //假设为当前服务器时间，这里采用的是本地时间，实际使用一般是取服务端的
    console.log(serverTime)
    util.countdown(endTime, serverTime, function(date, serverTime, timer){                                                                       
        var str = date[1] + ':' +  date[2] + ':' + date[3];
        layui.$('#time').html(str);
    });
    
    //表格
    var table = layui.table;
    table.init('tabledemo', {
        height:250 
        // ,limit: 10 
    });
    table.init('home-table', {
               
    });

})
