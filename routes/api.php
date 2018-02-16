<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

$api = app('Dingo\Api\Routing\Router');

$api->version('v1',['namespace'=>'App\Http\Controllers\Api'],function($api){
    $api->get('version',function(){
        return response('this is version v1');
    });
    //限制 频率 防刷
    $api->group([
        'middleware'=>'api.throttle',
        'limit'=>config('api.rate_limits.sign.limit'),
        'expires'=>config('api.rate_limits.sign.expires'),
    ],function ($api){
        //短信验证码
        $api->post('verificationsCodes','VerificationCodesController@store')
            ->name('api.verificationCodes.store');
        //用户注册接口
        $api->post('users','UsersController@store')
            ->name('api.users.store');
        //图像验证码 gregwar/captcha  这个包 不依赖与 session
        $api->post('captchas','CaptchasController@store')->name('api.captchas.store');
    });

});

$api->version('v2',function($api){
    $api->get('version',function(){
        return response('this is version v2');
    });
});
