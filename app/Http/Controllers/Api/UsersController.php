<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UsersRequest;
use App\Models\Image;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index ()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create (UsersRequest $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (UsersRequest $request)
    {
        // dd($request->toArray());
        $verifyData = \Cache::get($request->verification_key);

        if ( !$verifyData ) {
            return $this->response->error('验证码已经失效', 422);
        }

        //避免时序攻击
        if ( !hash_equals((string)$verifyData['code'], $request->verification_code) ) {
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::create([
            'name'     => $request->name,
            'phone'    => (string)$verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        \Cache::forget($request->verification_key);

        return $this->response->item($user, UserTransformer::class)
            ->setMeta([
                'access_token' => \Auth::guard('api')->fromUser($user),
                'token_type'   => 'Bearer',
                'expires_in'   => \Auth::guard('api')->factory()->getTTL() * 60,
            ])->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show ($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit ($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function update (UsersRequest $request)
    {
        //patch 修改部分资源
        $user = $this->user();

        $attributes = $request->all();

        if ( $request->avatar_image_id ) {
            $image                = Image::find($request->avatar_image_id);
            $attributes['avatar'] = $image->path;
        }

        $user->update($attributes);

        return $this->response->item($user, UserTransformer::class);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy ($id)
    {
        //
    }

    public function me ()
    {
        return $this->response->item($this->user(), UserTransformer::class);
    }

    public function activedIndex(User $user)
    {
        return $this->response->collection($user->getActiveUsers() , new UserTransformer());
    }
}
