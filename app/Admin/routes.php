<?php

use Illuminate\Routing\Router;

Admin::routes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('manage/create_user', 'CreateUserController');
    $router->resource('manage/user', 'UserController');
    $router->resource('manage/account', 'AccountDetailController');
    $router->resource('manage/config', 'ConfigController');
    $router->resource('manage/recharge', 'RechargeController');
    $router->resource('manage/atm', 'AtmController');;
    $router->get('/api/user/{id}', 'AdminUserController@parent');
    $router->resource('manage/level', 'LevelController');
    $router->resource('manage/notice', 'NoticeController');
    $router->resource('manage/bank', 'BankController');
    $router->resource('manage/order', 'OrderController');
    $router->resource('manage/ad', 'AdController');
    $router->resource('manage/lottery', 'LotteryController');
    $router->resource('manage/rank', 'RankController');

});
