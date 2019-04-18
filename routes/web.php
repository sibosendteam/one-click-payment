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



/***************************************
 ***************************************
 ********          支付模块       *******
 ***************************************
 ***************************************
 */
Route::group(['middleware' => 'web'], function () {
    Route::get('/', function () {
        return redirect('/one-click-payment');
    });

    Route::get('/one-click-payment', 'Recharge\PayController@index');
    Route::post('/register/vip', 'Recharge\PayController@postRegister');
    Route::post('/pay/wechat/notify', 'Recharge\PayNotifyController@postWechatNotify');
    Route::post('/pay/wechat/japi/notify', 'Recharge\PayNotifyController@postWeJsApiNotify');
    Route::post('/sms/code', 'Recharge\SmsCodeController@postSmsCode');
});











