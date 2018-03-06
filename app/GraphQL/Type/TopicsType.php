<?php
namespace App\GraphQL\Type;

use App\Models\Topic;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class TopicsType extends GraphQLType {

    protected $attributes = [
        'name'          => 'Topic',
        'description'   => 'topics',
        'model'         => Topic::class,
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the user',
            ],
            'title' => [
                'type' => Type::string(),
                'description' => 'The title of topics',
            ],
//            'limit' => [
//                'type' => Type::int(),
//                'description' => 'The limit of topics',
//            ],
        ];
    }
}