<?php

namespace App\Transformers;

use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class TopicTransformer extends TransformerAbstract
{
    //自动关联
    protected $availableIncludes = [
        'user',
        'category'
    ];

    public function transform (Topic $topic)
    {
        return [
            'id'                 => $topic->id,
            'title'              => $topic->title,
            'body'               => $topic->body,
            'reply_count'        => (int)$topic->reply_count,
            'view_count'         => (int)$topic->view_count,
            'last_reply_user_id' => (int)$topic->last_reply_user_id,
            'order'              => (int)$topic->order,
            'excerpt'            => $topic->excerpt,
            'slug'               => $topic->slug,
            'created_at'         => $topic->created_at->toDateTimeString(),
            'updated_at'         => $topic->updated_at->toDateTimeString(),
        ];
    }

    //$this->item 返回单个资源  $this->collection 返回资源集合
    public function includeUser (Topic $topic)
    {
        return $this->item($topic->user, new UserTransformer());
    }

    public function includeCategory (Topic $topic)
    {
        return $this->item($topic->category, new CategoryTransformer());
    }
}
