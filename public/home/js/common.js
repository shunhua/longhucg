/**
 * Created by root on 2018/5/18.
 */
function msg(msg) {
    layer.open({
      type: 1
      ,offset: 't' //具体配置参考：offset参数项
      ,content: '<div style="padding: 20px 80px;">'+msg+'</div>'
      ,time: 1 //2秒后自动关闭
    });
}
function msg_jump(msg,url) {
    layer.open({
        type: 1
        ,offset: 't' //具体配置参考：offset参数项
        ,content: '<div style="padding: 20px 80px;">'+msg+'</div>'
        ,time: 2 //2秒后自动关闭
        ,end:function () {
            location.href = url;
        }
    });
}

function msg_bsh_jump(msg,url='',msg1='') {
    layer.open({
        content: '<div class="showtext"><span class="setwin"><a href="javascript:layer.closeAll();" class="imgicon"></a></span><div class="test_m"><p>'+msg+'</p></div><div class="test_f"><span>'+msg1+'</span></div></div>'
        ,time: 2 //2秒后自动关闭
        ,end:function () {
            if (url) location.href = url; 
        }
    });

}
