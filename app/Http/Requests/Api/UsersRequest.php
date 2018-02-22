<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class UsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize ()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        switch ($this->method()) {
            case 'POST':
                return [
                    'name'              => 'required|string|max:255',
                    'password'          => 'required|string|max:255|min:6',
                    'verification_key'  => 'required|string',
                    'verification_code' => 'required|string',

                ];
                break;
            case 'PATCH':
                $userId = \Auth::guard('api')->id();
                return [
                    'name'            => 'unique:users,name|between:3,25',
                    'email'           => 'email|unique:users,email',
                    'introduction'    => 'max:80',
                    'avatar_image_id' => 'exists:images,id,type,avatar,user_id,' . $userId,//where语句后续如果拼接变量结尾要加逗号
                ];
                break;
        }

    }

    public function attributes ()
    {
        return [
            'verification_key'  => '短信验证码 key',
            'verification_code' => '短信验证码',
        ];
    }

    public function messages ()
    {
        return [
            'name.unique'  => '名称已存在',
            'name.between' => '名称必须介于3-25个字符',
        ];
    }
}
