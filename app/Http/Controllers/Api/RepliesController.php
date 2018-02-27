<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Transformers\ReplyTransformer;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index (Request $request, Topic $topic)
    {
        $replies = Reply::where('topic_id', $topic->id)->paginate(5);
        return $this->response->paginator($replies, new ReplyTransformer());
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
    public function store (Topic $topic, ReplyRequest $request, Reply $reply)
    {
        // one
//        $reply = Reply::create([
//            'topic_id' => $topic->id,
//            'content'  => $request->get('content'),
//            'user_id'  => $this->user()->id,
//        ]);

        //two
        $reply->topic_id = $topic->id;
        $reply->content  = $request->get('content');
        $reply->user_id  = $this->user()->id;
        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())
            ->setStatusCode(201);
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
    public function destroy (Topic $topic, Reply $reply)
    {
        if ( $topic->id !== $reply->topic_id ) {
            return $this->response->errorBadRequest('回复的话题与传入的值不符合');
        }

        $this->authorize('destroy', $reply);

        $reply->delete();

        return $this->response->noContent();
    }
}
