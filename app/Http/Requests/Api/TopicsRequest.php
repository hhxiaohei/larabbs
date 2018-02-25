<?php

namespace App\Http\Requests\Api;

class TopicsRequest extends BaseRequest
{
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
                    'title'       => 'required|string',
                    'body'        => 'required|string|max:255',
                    'category_id' => 'required|exists:categories,id',
                ];
                break;
            case 'PATCH':
                //因为 patch 只提供部分信息,所以不是都是必填
                return [
                    'title'       => 'string',
                    'body'        => 'string|max:255',
                    'category_id' => 'exists:categories,id',
                ];
                break;
        }

    }

    public function attributes ()
    {
        return [
            'tilte'       => '标题',
            'body'        => '内容',
            'category_id' => '分类',
        ];
    }
}
