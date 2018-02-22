<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class ImagesRequest extends FormRequest
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
        $rules = [
            'type'  => 'required|in:avatar,topic',
            'image' => 'mimes:jpeg,bmp,png,gif',
        ];

        if ( $this->type == 'avatar' ) {//头像有宽高限制dimensions
            $rules['image'] = 'mimes:jpeg,bmp,png,gif|dimensions:min_width=200,min_height=200';
        }

        return $rules;
    }

    public function messages ()
    {
        return [
            'image.dimensions' => '最少宽200px 高200px',
        ];
    }
}
