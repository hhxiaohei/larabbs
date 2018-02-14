<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Overtrue\EasySms\EasySms;
use App\Http\Requests\Api\VerificationCodeRequest;

class VerificationCodesController extends Controller
{
    public function store(VerificationCodeRequest $request){
        
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
            return $this->respnse->errorInternal($result['msg']??'短信发送异常');
        }

        $expiredAt = now()->addMinutes(10);
        \Cache::put('verficationCode_'.$code , [
            'phone'=>$phone,
            'code'=>$code,
        ],$expiredAt);

        return $this->response->array([
            'key'=>'verficationCode_'.$code,
            'expired_at'=>$expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
