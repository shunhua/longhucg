<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['namespace' => 'Home'], function () {
	Route::any('login.html', ['as' => 'login',  'uses' => 'AuthController@login']);
	Route::any('loginauth.html', ['as' => 'loginauth',  'uses' => 'AuthController@loginAuth']);
	
    Route::group(['middleware' => ['auth']], function () {

    	Route::any('save', ['as' => 'save',  'uses' => 'UserController@save']);
		Route::any('saveinfo', ['as' => 'saveinfo',  'uses' => 'UserController@saveinfo']);
		
    	Route::group(['middleware' => ['checkimprove']], function () {
	        Route::any('/', ['as' => '/',  'uses' => 'IndexController@index']);
	        Route::any('statistics', ['uses' => 'IndexController@statistics']);
	        Route::any('trend', ['as' => 'trend',  'uses' => 'IndexController@trend']);
	        Route::any('guide', ['as' => 'guide',  'uses' => 'IndexController@guide']);

	        Route::any('order/add.html', ['uses' => 'OrderController@add']);

	        Route::any('user/index.html', ['as' => 'user/index',  'uses' => 'UserController@index']);
	        Route::any('user/account.html', ['as' => 'user/account',  'uses' => 'UserController@account']);
	        Route::any('user/recharge.html', ['as' => 'user/recharge',  'uses' => 'UserController@recharge']);
	        Route::any('user/pay.html', ['as' => 'user/pay',  'uses' => 'UserController@pay']);
	        Route::any('user/withdraw.html', ['as' => 'user/withdraw',  'uses' => 'UserController@withdraw']);
	        Route::any('user/atm.html', ['as' => 'user/atm',  'uses' => 'UserController@atm']);
	        Route::any('user/bindcard.html', ['as' => 'user/bindcard',  'uses' => 'UserController@bindcard']);
	        Route::any('user/savebank.html', ['as' => 'user/savebank',  'uses' => 'UserController@savebank']);
	        Route::any('card/remove.html', ['as' => 'card/remove',  'uses' => 'UserController@cardRemove']);
	        Route::any('atm/sub.html', ['as' => 'atm/sub',  'uses' => 'UserController@atmSub']);
	        Route::any('pay/sub.html', ['as' => 'pay/sub',  'uses' => 'UserController@paySub']);
	        Route::any('notice.html', ['as' => 'notice',  'uses' => 'UserController@notice']);
	        Route::any('notice/read.html', ['as' => 'notice/read',  'uses' => 'UserController@markRead']);
	    });



	    Route::any('logout.html', ['as' => 'logout',  'uses' => 'AuthController@logout']);
    });

});
