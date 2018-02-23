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

$api->version('v1', [
    'namespace'  => 'App\Http\Controllers\Api',
    'middleware' => ['serializer:array', 'bindings'],//serializer:data_array 包一层 data, serializer:array 为去除 data 的数据格式
], function ($api) {
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
        //微信授权登录
        $api->post('social/{social_type}/authorizations' , 'AuthorizationsController@socialStore')->name('api.social.wechat.store');
        // token登录
        $api->post('authorizations','AuthorizationsController@store')->name('api.authorizations.store');
        //刷新 token
        $api->put('authorizations/current','AuthorizationsController@update')->name('api.authorizations.update');
        //删除 token
        $api->delete('authorizations/current','AuthorizationsController@delete')->name('api.authorizations.delete');
    });

    $api->group([
        'middleware' => 'api.throttle',
        'limit'      => config('api.rate_limits.access.limit'),
        'expires'    => config('api.rate_limits.access.expires'),
    ], function ($api) {
        //游客 api
        //分类
        $api->get('categories', 'CategoriesController@index')->name('api.categories.index');

        //用户 api
        $api->group([
            'middleware' => 'api.auth',
        ], function ($api) {
            $api->get('user', 'UsersController@me')->name('api.user.me');
            //更新用户信息(patch 为更新部分数据  put 为修改全部数据)
            $api->patch('user', 'UsersController@update')->name('api.user.update');
            //图片上传
            $api->post('images', 'ImagesController@store');
            //发布话题
            $api->post('topic', 'TopicsController@store')->name('api.topic.store');
            //编辑话题
            $api->patch('topic/{topic}', 'TopicsController@update')->name('api.topic.update');
            //删除话题
            $api->delete('topic/{topic}', 'TopicsController@destroy')->name('api.topic.destroy');
        });

    });
});

$api->version('v2',function($api){
    $api->get('version',function(){
        return response('this is version v2');
    });
});
