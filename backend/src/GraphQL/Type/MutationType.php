<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Database\Database;

class MutationType {
    public static function build() {
        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createOrder' => [
                    'type' => Type::string(),
                    'args' => [
                        'customerName' => Type::nonNull(Type::string()),
                        'customerEmail' => Type::nonNull(Type::string()),
                        'orderData' => Type::nonNull(Type::string())
                    ],
                    'resolve' => function($root, $args) {
                        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
                        $dotenv->load();
                        $db = new Database(
                            $_ENV['DB_HOST'],
                            $_ENV['DB_NAME'],
                            $_ENV['DB_USER'],
                            $_ENV['DB_PASS']
                        );
                        $pdo = $db->getConnection();
                        $stmt = $pdo->prepare("INSERT INTO orders (customer_name, customer_email, order_data) VALUES (?, ?, ?)");
                        $stmt->execute([$args['customerName'], $args['customerEmail'], $args['orderData']]);
                        return "Order created successfully";
                    }
                ]
            ]
        ]);
    }
}