<?php
namespace App\GraphQL;

use GraphQL\Type\Schema;
use App\GraphQL\Type\QueryType;
use App\GraphQL\Type\MutationType;

class MainSchema {
    public static function build() {
        return new Schema([
            'query' => QueryType::build(),
            'mutation' => MutationType::build()
        ]);
    }
}