<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use App\Traits\PassportToken;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;
use Illuminate\Http\Request;
use League\OAuth2\Server\AuthorizationServer;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthorizationsController extends Controller
{
    use PassportToken;

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
            $result = $this->getBearerTokenByUser($user , 1 ,false);
            return $this->response->array($result)->setStatusCode(201);
//            return $this->respondWithToken(\Auth::guard('api')->fromUser($user));
        } catch (JWTException $e) {
            return $this->response->error('could_not_create_token');
        }
    }

    //passport oauth2 生成 token
    public function store (AuthorizationsRequest $originRequest , AuthorizationServer $server,ServerRequestInterface $serverRequest)
    {
        try{
            return $server->respondToAccessTokenRequest($serverRequest,new Psr7Response)->withStatus(201);
        }catch (\OAuthException $exception){
            return $this->response->errorUnauthorized($exception->getMessage());
        }
//        $username = $request->username;
//
//        //邮箱格式判断
//        filter_var($username, FILTER_VALIDATE_EMAIL) ?
//            $form['email'] = $username :
//            $form['phone'] = $username;
//
//        $form['password'] = $request->password;
//
//        try {
//            if ( !$token = JWTAuth::attempt($form) ) {
////                return $this->errorResponse(403,'aaa',1001);
//                return $this->response->errorUnauthorized(trans('auth.failed'));
//            }
//            return $this->respondWithToken($token);
//        } catch (JWTException $e) {
//            return $this->response->error('could_not_create_token');
//        }

    }

    //passport 改造 暂时去掉 request 验证
    public function update (AuthorizationServer $server,ServerRequestInterface $serverRequest)
    {
//        $token = \Auth::guard('api')->refresh();
//        return $this->respondWithToken($token);
        try{
            return $server->respondToAccessTokenRequest($serverRequest,new Psr7Response);
        }catch (\OAuthException $exception){
            return $this->response->errorUnauthorized($exception->getMessage());
        }
    }

    public function delete ()
    {
//        \Auth::guard('api')->logout();
        //删除 204
        $this->user()->token()->revoke();
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
