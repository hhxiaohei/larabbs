<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    public function transform (User $user)
    {
        return [
            'id'              => $user->id,
            'name'            => $user->name,
            'email'           => $user->email,
            'avatar'          => $user->avatar,
            'bound_wechat'    => (bool)($user->wechat_openid || $user->wechat_unionid),
            'bound_phone'     => (bool)$user->phone,
            'introduction'    => $user->introduction,
            'last_actived_at' => $user->last_actived_at->toDateTimeString(),
            'created_at'      => $user->created_at->toDateTimeString(),
            'updated_at'      => $user->updated_at->toDateTimeString(),
        ];
    }
}
