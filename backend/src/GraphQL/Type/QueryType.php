<?php
namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use App\Database\Database;

class QueryType {
    public static function build() {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'products' => [
                    'type' => Type::listOf(ProductType::get()),
                    'resolve' => function() {
                        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
                        $dotenv->load();
                        $db = new Database(
                            $_ENV['DB_HOST'],
                            $_ENV['DB_NAME'],
                            $_ENV['DB_USER'],
                            $_ENV['DB_PASS']
                        );
                        $pdo = $db->getConnection();
                        $stmt = $pdo->query("SELECT * FROM products");
                        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);

                        // Fetch related data for each product (gallery, attributes, prices)
                        foreach ($products as &$product) {
                            // Gallery
                            $stmtGallery = $pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = ?");
                            $stmtGallery->execute([$product['id']]);
                            $product['gallery'] = array_column($stmtGallery->fetchAll(\PDO::FETCH_ASSOC), 'image_url');

                            // Attributes
                            $stmtAttr = $pdo->prepare("SELECT * FROM attributes WHERE product_id = ?");
                            $stmtAttr->execute([$product['id']]);
                            $attributes = $stmtAttr->fetchAll(\PDO::FETCH_ASSOC);
                            foreach ($attributes as &$attr) {
                                $stmtItems = $pdo->prepare("SELECT * FROM attribute_items WHERE product_id = ? AND attr_id = ?");
                                $stmtItems->execute([$product['id'], $attr['attr_id']]);
                                $dbItems = $stmtItems->fetchAll(\PDO::FETCH_ASSOC);
                                $items = [];
                                foreach ($dbItems as $dbItem) {
                                    $items[] = [
                                        'id' => $dbItem['item_id'],
                                        'displayValue' => $dbItem['display_value'],
                                        'value' => $dbItem['value']
                                    ];
                                }
                                $attr['items'] = $items;
                            }
                            $product['attributes'] = $attributes;

                            // Prices
                            $stmtPrices = $pdo->prepare("SELECT * FROM prices WHERE product_id = ?");
                            $stmtPrices->execute([$product['id']]);
                            $prices = $stmtPrices->fetchAll(\PDO::FETCH_ASSOC);
                            foreach ($prices as &$price) {
                                $price['currency'] = [
                                    'label' => $price['currency_label'],
                                    'symbol' => $price['currency_symbol']
                                ];
                            }
                            $product['prices'] = $prices;
                        }

                        return $products;
                    }
                ],
                'categories' => [
                    'type' => Type::listOf(Type::string()),
                    'resolve' => function() {
                        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
                        $dotenv->load();
                        $db = new Database(
                            $_ENV['DB_HOST'],
                            $_ENV['DB_NAME'],
                            $_ENV['DB_USER'],
                            $_ENV['DB_PASS']
                        );
                        $pdo = $db->getConnection();
                        $stmt = $pdo->query("SELECT name FROM categories");
                        $categories = $stmt->fetchAll(\PDO::FETCH_COLUMN);
                        return $categories;
                    }
                ],
                'product' => [
                    'type' => ProductType::get(),
                    'args' => [
                        'id' => Type::nonNull(Type::string())
                    ],
                    'resolve' => function($root, $args) {
                        $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../../../');
                        $dotenv->load();
                        $db = new \App\Database\Database(
                            $_ENV['DB_HOST'],
                            $_ENV['DB_NAME'],
                            $_ENV['DB_USER'],
                            $_ENV['DB_PASS']
                        );
                        $pdo = $db->getConnection();
                        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
                        $stmt->execute([$args['id']]);
                        $product = $stmt->fetch(\PDO::FETCH_ASSOC);
                        if (!$product) {
                            throw new \Exception("Product not found");
                        }
                        // Fetch gallery, attributes, prices as in products resolver
                        $stmtGallery = $pdo->prepare("SELECT image_url FROM product_gallery WHERE product_id = ?");
                        $stmtGallery->execute([$product['id']]);
                        $product['gallery'] = array_column($stmtGallery->fetchAll(\PDO::FETCH_ASSOC), 'image_url');
                        $stmtAttr = $pdo->prepare("SELECT * FROM attributes WHERE product_id = ?");
                        $stmtAttr->execute([$product['id']]);
                        $attributes = $stmtAttr->fetchAll(\PDO::FETCH_ASSOC);
                        foreach ($attributes as &$attr) {
                            $stmtItems = $pdo->prepare("SELECT * FROM attribute_items WHERE product_id = ? AND attr_id = ?");
                            $stmtItems->execute([$product['id'], $attr['attr_id']]);
                            $dbItems = $stmtItems->fetchAll(\PDO::FETCH_ASSOC);
                            $items = [];
                            foreach ($dbItems as $dbItem) {
                                $items[] = [
                                    'id' => $dbItem['item_id'],
                                    'displayValue' => $dbItem['display_value'],
                                    'value' => $dbItem['value']
                                ];
                            }
                            $attr['items'] = $items;
                        }
                        $product['attributes'] = $attributes;
                        $stmtPrices = $pdo->prepare("SELECT * FROM prices WHERE product_id = ?");
                        $stmtPrices->execute([$product['id']]);
                        $prices = $stmtPrices->fetchAll(\PDO::FETCH_ASSOC);
                        foreach ($prices as &$price) {
                            $price['currency'] = [
                                'label' => $price['currency_label'],
                                'symbol' => $price['currency_symbol']
                            ];
                        }
                        $product['prices'] = $prices;
                        return $product;
                    }
                ]
            ]
        ]);
    }
}


/**
 
{
  "query": "{ products { id name category description inStock gallery brand attributes { id name type items { id displayValue value } } prices { amount currency { label symbol } } } }"
}

{
  "query": "{ product(id: \"apple-airpods-pro\") { id name category description inStock gallery brand attributes { id name type items { id displayValue value } } prices { amount currency { label symbol } } } }"
}

{
  "query": "mutation { createOrder(customerName: \"John Doe\", customerEmail: \"john@example.com\", orderData: \"{\\\"items\\\":[{\\\"productId\\\":\\\"apple-airpods-pro\\\",\\\"quantity\\\":1}]}\") }"
}

 */