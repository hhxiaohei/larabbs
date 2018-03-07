<?php
namespace App\GraphQL\Mutation;

use App\Models\Topic;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class TopicsMutation extends Mutation {

    protected $attributes = [
        'name' => 'topics'
    ];

    //返回格式 对应 /app/GraphQL/Type/TopicsType.php
    public function type()
    {
        return GraphQL::type('topic');
    }

    //传入参数 加验证
    public function args()
    {
        return [
            'title' => ['name' => 'title', 'type' => Type::nonNull(Type::string()),'rules'=>['required']],
            'body' => ['name' => 'body', 'type' => Type::nonNull(Type::string())],
            'user_id' => ['name' => 'user_id', 'type' => Type::nonNull(Type::int())],
            'category_id' => ['name' => 'category_id', 'type' => Type::nonNull(Type::int())]
        ];
    }

    //创建
    public function resolve($root, $args)
    {
        $topic = Topic::create([
            'title'   => $args['title'],
            'body'    => $args['body'],
            'user_id' => $args['user_id'],
            'category_id' => $args['category_id'],
        ]);

        return $topic;
    }

}