@extends('layouts.app')

@section('title', '号码走势')

@section('headScript')
    <link rel="stylesheet" href="{{asset('css/app.css')}}">
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/css/index.css'); ?>"> 
    <link type="text/css" rel="stylesheet" href="<?php echo loadStatic('/home/layui/css/layui.css'); ?>">
@endsection

@section('content')
    <div class="number_trend">
            <p>号码走势</p>
                <table class="layui-table" lay-size="sm">
                        <colgroup>
                          <col width="80">
                          <col width="50">
                          <col>
                        </colgroup>
                        <thead>
                                <tr>
                                  <th rowspan="3">期数</th>
                                  <th rowspan="3">开奖号码</th>
                                  <th colspan="10">万位</th>
                                  <th colspan="10">千位</th>
                                  <th colspan="10">百位</th>
                                  <th colspan="10">十位</th>
                                  <th colspan="10">个位</th>
                                </tr>
                                <tr>
                                  <th>0</th>
                                  <th>1</th>
                                  <th>2</th>
                                  <th>3</th>
                                  <th>4</th>
                                  <th>5</th>
                                  <th>6</th>
                                  <th>7</th>
                                  <th>8</th>
                                  <th>9</th>
                                  <th>0</th>
                                  <th>1</th>
                                  <th>2</th>
                                  <th>3</th>
                                  <th>4</th>
                                  <th>5</th>
                                  <th>6</th>
                                  <th>7</th>
                                  <th>8</th>
                                  <th>9</th>
                                  <th>0</th>
                                  <th>1</th>
                                  <th>2</th>
                                  <th>3</th>
                                  <th>4</th>
                                  <th>5</th>
                                  <th>6</th>
                                  <th>7</th>
                                  <th>8</th>
                                  <th>9</th>
                                  <th>0</th>
                                  <th>1</th>
                                  <th>2</th>
                                  <th>3</th>
                                  <th>4</th>
                                  <th>5</th>
                                  <th>6</th>
                                  <th>7</th>
                                  <th>8</th>
                                  <th>9</th>
                                  <th>0</th>
                                  <th>1</th>
                                  <th>2</th>
                                  <th>3</th>
                                  <th>4</th>
                                  <th>5</th>
                                  <th>6</th>
                                  <th>7</th>
                                  <th>8</th>
                                  <th>9</th>
                                  
                                </tr>
                              </thead>
                        <tbody>
                          @if (!$lotterys->isEmpty())
                          @foreach ($lotterys as $lottery)
                          <tr>
                            <td>{{$lottery->periods}}</td>
                            <td>{{str_replace(',', ' ', $lottery->open_number)}}</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 0)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 1)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 2)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 3)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 4)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 5)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 6)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 7)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 8)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[0] == 9)<i class="blur"></i>@endif</td>
                            <!-- 千位 -->
                            <td>@if(explode(',',$lottery->open_number)[1] == 0)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 1)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 2)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 3)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 4)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 5)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 6)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 7)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 8)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[1] == 9)<i class="blur"></i>@endif</td>
                            <!-- 百位 -->
                            <td>@if(explode(',',$lottery->open_number)[2] == 0)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 1)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 2)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 3)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 4)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 5)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 6)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 7)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 8)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[2] == 9)<i class="blur"></i>@endif</td>
                            <!-- 十位 -->
                            <td>@if(explode(',',$lottery->open_number)[3] == 0)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 1)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 2)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 3)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 4)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 5)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 6)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 7)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 8)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[3] == 9)<i class="blur"></i>@endif</td>
                            <!-- 个位 -->
                            <td>@if(explode(',',$lottery->open_number)[4] == 0)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 1)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 2)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 3)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 4)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 5)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 6)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 7)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 8)<i class="blur"></i>@endif</td>
                            <td>@if(explode(',',$lottery->open_number)[4] == 9)<i class="blur"></i>@endif</td>
                          </tr>
                          @endforeach  
                          @endif
                        </tbody>
                      </table>
                      <!-- 分页 -->
                      {!! $lotterys->links() !!}
        </div>
@endsection
