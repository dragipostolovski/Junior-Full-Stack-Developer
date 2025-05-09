<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType extends ObjectType {
    public function __construct() {
        parent::__construct([
            'name' => 'Product',
            'fields' => [
                'id' => Type::nonNull(Type::string()),
                'name' => Type::nonNull(Type::string()),
                'category' => Type::nonNull(Type::string()),
                'description' => Type::string(),
                'inStock' => Type::boolean(),
                'gallery' => Type::listOf(Type::string()),
                'brand' => Type::string(),
                'attributes' => Type::listOf(AttributeSetType::get()),
                'prices' => Type::listOf(PriceType::get())
            ]
        ]);
    }

    public static function get() {
        return new self();
    }
}