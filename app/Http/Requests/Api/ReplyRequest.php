<?php

namespace App\Http\Requests\Api;

class ReplyRequest extends BaseRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules ()
    {
        return [
            'content' => 'required|min:2|max:200',
        ];
    }

    public function attributes ()
    {
        return [
            'content' => '内容',
        ];
    }
}
