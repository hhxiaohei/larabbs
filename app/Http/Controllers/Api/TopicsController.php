<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicsRequest;
use App\Models\Topic;
use App\Transformers\TopicTransformer;
use Illuminate\Http\Request;

class TopicsController extends Controller
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
    public function create ()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store (TopicsRequest $request, Topic $topic)
    {
        //第一次用 fill  看源码为循环 赋值 attributes
        $topic->fill($request->toArray());
        $topic->user_id = $this->user()->id;
        $topic->save();

        //保存成功务必记得改状态为201
        return $this->response->item($topic, TopicTransformer::class)->setStatusCode(201);
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
     * @param  App\Models\Topic $topic
     * @return \Illuminate\Http\Response
     */
    public function update (TopicsRequest $request, Topic $topic)
    {

//快速鉴权 参考 https://www.jianshu.com/p/99f6320fe142
        $this->authorize('update', $topic);

        $topic->update($request->toArray());

        return $this->response->item($topic, TopicTransformer::class);
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
