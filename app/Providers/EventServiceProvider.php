<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],
        \SocialiteProviders\Manager\SocialiteWasCalled::class => [
            'SocialiteProviders\Weixin\WeixinExtendSocialite@handle'
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
//token 直接换用户信息
//$accessToken = '';
//$openID = ''
//$driver = Socialite::driver('weixin');
//$driver->setOpenId($openID);
//$oauthUser = $driver->userFromToken($accessToken);


//code 换 token 然后 token 换用户信息
//$code = 'http://l5bbs.dev/?code=071xL0F52d9kdK0bF3F524BjF52xL0Fz&state=STATE';
//$driver = Socialite::driver('weixin');
//$response = $driver->getAccessTokenResponse($code);
//$driver->setOpenId($response['openid']);
//$oauthUser = $driver->userFromToken($response['access_token']);

//https://open.weixin.qq.com/connect/oauth2/authorize?appid=wx280a0718a260f752&redirect_uri=http://l5bbs.dev&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect

}
