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
        //话题
        $api->get('topics', 'TopicsController@index')->name('api.topics.index');
        $api->get('users/{user}/topics', 'TopicsController@userIndex')->name('api.users.topics.index');
        //单条话题详情
        $api->get('topics/{topic}', 'TopicsController@show')->name('api.topics.show');
        $api->get('topics/{topic}/replies', 'RepliesController@index')->name('api.topics.replies.index');

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

            //发表回复
            $api->post('topics/{topic}/replies', 'RepliesController@store')->name('api.topic.reply.store');
            //删除某个话题的某个回复(权限控制 )
            $api->delete('topics/{topic}/replies/{reply}', 'RepliesController@destroy')->name('api.topic.reply.destroy');
        });

    });
});

$api->version('v2',function($api){
    $api->get('version',function(){
        return response('this is version v2');
    });
});
