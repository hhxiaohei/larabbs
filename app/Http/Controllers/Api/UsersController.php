<?php

namespace App\Http\Controllers\Api;

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

        User::create([
            'name'     => $request->name,
            'phone'    => (string)$verifyData['phone'],
            'password' => bcrypt($request->password),
        ]);

        \Cache::forget($request->verification_key);

        return $this->response->created();
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
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function update (Request $request, $id)
    {
        //
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
}
