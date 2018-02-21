<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchasRequest;
use Gregwar\Captcha\CaptchaBuilder;

class CaptchasController extends Controller
{
    public function store (CaptchasRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key        = 'captchas_' . str_random(20);
        $phone      = $request->phone;
        $expired_at = now()->addMinute(2);
        $captcha    = $captchaBuilder->build();

        //缓存放 随机 key 加验证码信息
        \Cache::put($key, [
            'phone' => $phone,
            'code'  => $captcha->getPhrase(),
        ], $expired_at);

        //返回图片信息和 key
        return $this->response->array([
            'captcha_key'           => $key,
            'expired_at'            => $expired_at->toDateTimeString(),
            'captcha_image_content' => $captcha->inline()
        ])->setStatusCode(200);
    }
}
