@extends('layouts.app')

@section('title', '新手指导')

@section('headScript')
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
    <div class="new_guide center">
        <p>新手指导</p>
        <div class="guide_t">
             {!! _config('guide') !!}
        </div>
    </div>
@endsection
