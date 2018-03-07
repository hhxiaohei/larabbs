<?php

namespace App\GraphQL\Query;

use GraphQL;
use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Query;
use App\Models\User;
use Rebing\GraphQL\Support\SelectFields;

class UsersQuery extends Query {

    protected $attributes = [
        'name' => 'Users query'
    ];

    //authorize 鉴权
    public function authorize(array $args)
    {
        return ! \Auth::guest();
    }

    //加分页
    public function type()
    {
        return GraphQL::paginate('user');
    }

    public function args()
    {
        return [
            'id'         => ['name' => 'id', 'type' => Type::string()],
            'email'      => ['name' => 'email', 'type' => Type::string()],
            'name'       => ['name' => 'name', 'type' => Type::string()],
            'created_at' => ['name' => 'created_at', 'type' => Type::string()],
            'limit' => ['name' => 'limit', 'type' => Type::int()],
            'page' => ['name' => 'page', 'type' => Type::int()],
        ];
    }

    public function resolve($root, $args,SelectFields $fields)
    {
        return  User::select($fields->getSelect())->paginate($args['limit'], ['*'], 'page', $args['page']);;
    }

}