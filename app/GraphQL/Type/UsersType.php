<?php
namespace App\GraphQL\Type;

use App\Models\User;
use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class UsersType extends GraphQLType {

    protected $attributes = [
        'name'          => 'User',
        'description'   => 'A user',
        'model'         => User::class,
    ];

    public function fields()
    {
        return [
            'id' => [
                'type' => Type::nonNull(Type::string()),
                'description' => 'The id of the user',
            ],
            'email' => [
                'type' => Type::string(),
                'description' => 'The email of user',
            ],
            'name' => [
                'type' => Type::string(),
                'description' => 'The name of user',
            ],
            'created_at' => [
                'type' => Type::string(),
                'description' => 'The created_at of user',
            ],
            'topics' => [
                'type' => Type::listOf(GraphQL::type('topic')),
                'description' => 'The topics of user',
            ],
        ];
    }

    protected function resolveCreatedAtField($root , $args){
        return (string)$root->created_at;
    }

}