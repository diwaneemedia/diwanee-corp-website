<?php
namespace App\GraphQL\Query;

use App\Node;

use GraphQL\Type\Definition\Type;
use GraphQL\Type\Definition\ResolveInfo;
use Rebing\GraphQL\Support\SelectFields;
use Rebing\GraphQL\Support\Query;
use Rebing\GraphQL\Support\Facades\GraphQL;

class NodesNmQuery extends Query {
    protected $name = '';
    
    protected $args = [];
    
    public function __construct($attributes = array()) {
        parent::__construct($attributes);
        
        $this->attributes['name'] = $this->name . 'sQuery';
        $this->attributes['description'] = 'A query';
    }

    public function type() {
        return Type::listOf(GraphQL::type($this->name));
    }

    public function args() {
        $args = [
            'id' => [
                'type' => Type::int(),
                'name' => 'id'
            ],
            'node_id' => [
                'type' => Type::int(),
                'name' => 'node_id'
            ]
        ];
        
        foreach($this->args as $argName => $argType) {
            $args[$argName] = [
                'type' => Type::$argType(),
                'name' => $argName
            ];
        }
        
        return $args;
    }

    public function resolve($root, $args, SelectFields $fields, ResolveInfo $info) {
        $where = function ($query) use ($args) {
            if (isset($args['id'])) {
                $query->where('id', $args['id']);
            }
            
            if (isset($args['node_id'])) {
                $query->where('node_id', $args['node_id']);
            }
        };
        
        $items = null;
        if($this->name !== '') {
            $model = 'App\\NodeModel\\' . $this->name;
            $items = $model::with(array_keys($fields->getRelations()))
                ->where($where)
                ->select(array_merge($fields->getSelect()))
                ->paginate();
        }
        
        return $items;
    }
}