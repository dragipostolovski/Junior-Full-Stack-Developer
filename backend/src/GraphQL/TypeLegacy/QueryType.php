<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class QueryType {
    public static function build() {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf(ProductType::get()),
                    'resolve' => function() {
                        // Fetch products from DB and return as array
                        // ... existing code ...
                    }
                ],
                'categories' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => function() {
                        // Fetch categories from DB and return as array
                        // ... existing code ...
                    }
                ]
            ]
        ]);
    }
}