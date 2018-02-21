<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthorizationsController extends Controller
{
    public function socialStore ($type, SocialAuthorizationRequest $request)
    {
        if ( !in_array($type, ['weixin']) ) {
            return $this->response->errorBadRequest('必须是微信登录');
        }

        $driver = \Socialite::driver($type);

        switch ($type) {
            case 'weixin':

                try {
                    if ( $code = $request->code ) {
                        $response = $driver->getAccessTokenResponse($code);
                        $token    = array_get($response, 'access_token');
                    }

                    $oauthUser = $driver->userFromToken($token);
                } catch (\Exception $e) {
                    return $this->response->errorUnauthorized('code错误');
                }

                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ( $unionid ) {
                    $user = User::where('wechat_unionid', $unionid)->first();
                }
                else {
                    $user = User::where('wechat_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if ( !$user ) {
                    $user = User::create([
                        'name'           => $oauthUser->getNickname(),
                        'avatar'         => $oauthUser->getAvatar(),
                        'wechat_openid'  => $oauthUser->getId(),
                        'wechat_unionid' => $unionid,
                    ]);
                }

                break;
        }
        //创建 token
        try {
            return $this->respondWithToken(\Auth::guard('api')->fromUser($user));
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token');
        }
    }

    public function store (AuthorizationsRequest $request)
    {
        $username = $request->username;

        //邮箱格式判断
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $form['email'] = $username :
            $form['phone'] = $username;

        $form['password'] = $request->password;

        try {
            if ( !$token = JWTAuth::attempt($form) ) {
                return $this->response->errorUnauthorized('用户名或者密码错误');
            }
            return $this->respondWithToken($token);
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token');
        }
    }

    public function update ()
    {
        $token = \Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    public function delete ()
    {
        \Auth::guard('api')->logout();
        //删除 204
        return $this->response->noContent();
    }

    private function respondWithToken ($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'expires_in'   => \Auth::guard('api')->factory()->getTTL() * 60,
        ])->setStatusCode(201);
    }
}
