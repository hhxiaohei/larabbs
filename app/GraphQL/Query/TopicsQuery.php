<?php

namespace App\GraphQL\Query;

use App\Models\Topic;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;

class TopicsQuery extends Query {

    protected $attributes = [
        'name' => 'Topics query'
    ];

    public function type()
    {
        return Type::listOf(GraphQL::type('topic'));
    }

    public function args()
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::string()],
            'title' => ['name' => 'title', 'type' => Type::string()],
//            'limit' => ['name' => 'limit', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args)
    {
        $query = new Topic();
        if(isset($args['limit'])){
            $query->limit($args['limit']);
        }
        return $query->where(array_except($args,'limit'))->get();
    }

}