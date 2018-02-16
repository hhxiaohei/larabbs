<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    /*
     200 OK - 对成功的 GET、PUT、PATCH 或 DELETE 操作进行响应。也可以被用在不创建新资源的 POST 操作上
    201 Created - 对创建新资源的 POST 操作进行响应。应该带着指向新资源地址的 Location 头
    202 Accepted - 服务器接受了请求，但是还未处理，响应中应该包含相应的指示信息，告诉客户端该去哪里查询关于本次请求的信息
    204 No Content - 对不会返回响应体的成功请求进行响应（比如 DELETE 请求）
    304 Not Modified - HTTP缓存header生效的时候用
    400 Bad Request - 请求异常，比如请求中的body无法解析
    401 Unauthorized - 没有进行认证或者认证非法
    403 Forbidden - 服务器已经理解请求，但是拒绝执行它
    404 Not Found - 请求一个不存在的资源
    405 Method Not Allowed - 所请求的 HTTP 方法不允许当前认证用户访问
    410 Gone - 表示当前请求的资源不再可用。当调用老版本 API 的时候很有用
    415 Unsupported Media Type - 如果请求中的内容类型是错误的
    422 Unprocessable Entity - 用来表示校验错误
    429 Too Many Requests - 由于请求频次达到上限而被拒绝访问
     */
    public function store(VerificationCodeRequest $request){

        //验证图像验证码 1.缓存是否存在  2.与缓存中的值是否一致 错误则删除缓存

        $captcha_key = $request->captcha_key;
        $captcha_code = $request->captcha_code;

        $captcha_cache = \Cache::get($captcha_key);

        if(!$captcha_cache)
        {
            return $this->response->error('验证码已经失效',422);
        }

        if(!hash_equals($captcha_cache['code'] , (string)$captcha_code))
        {
            \Cache::forget($captcha_key);
            return $this->response->error('验证码错误',422);
        }

        $phone = $request->phone;

        $code = str_pad(random_int(1,9999),4,0,STR_PAD_LEFT);

        try{
            if(env('APP_DEBUG')){
                $code = 1234;
            }else{
                $result = $easySms->send($phone , [
                    'content'=>"您的验证码是{$code}",
                ]);
            }
        }catch(\GuzzleHttp\Exception\ClientException $e){
            $response = $e->getResponse();
            $result = json_decode($response->getBody()->getContents(),1);
            return $this->response->errorInternal($result['msg']??'短信发送异常');
        }

        $expiredAt = now()->addMinutes(10);
        \Cache::put('verficationCode_'.$phone , [
            'phone'=>$phone,
            'code'=>$code,
        ],$expiredAt);

        return $this->response->array([
            'key'=>'verficationCode_'.$phone,
            'expired_at'=>$expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
