@extends('layouts.app')

@section('title', '消息中心')

@section('headScript')
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
    <div class="news_m center">
        <p>消息中心</p>
        <ul>
            @if (!$notices->isEmpty())
                @foreach ($notices as $notice)
                    <li>
                        {{ $notice->contents }}
                        <p>{{ $notice->created_at->format('Y.m.d H:i') }}</p>
                        <a href="javascript:;" title='标记已读' onclick="read(<?php echo $notice->id?>,this)"><span class="read @if (!$notice->status) layui-badge-dot @endif "></span></a>
                    </li>
                @endforeach  
            @else
                <div class="notable">
                    <p>暂无数据</p>
                </div>
            @endif  
            
        </ul>
    </div>
@endsection

@section('footerScript')
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/jquery.min.js'); ?>"></script>
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/common.js'); ?>"></script>  
    <script type="text/javascript" src="<?php echo loadStatic('/home/js/layer.js'); ?>"></script>  
    <script>
        var flag=1;
        function read(id,obj) {
            $.ajax({
                url:"/notice/read.html",
                data:{
                    id:id
                },
                type:'post',
                dataType:'json',
                success:function (data) {
                    flag=1;
                    if(data.result == 'success'){
                        $('.read').removeClass('layui-badge-dot');
                    }
                }
            });
        }  
    </script>
@endsection