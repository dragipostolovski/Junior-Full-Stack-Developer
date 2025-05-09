<?php
require_once __DIR__ . '/vendor/autoload.php';

use App\Database\Database;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$db = new Database(
    $_ENV['DB_HOST'],
    $_ENV['DB_NAME'],
    $_ENV['DB_USER'],
    $_ENV['DB_PASS']
);

$pdo = $db->getConnection();

$json = file_get_contents(__DIR__ . '/data.json');
$data = json_decode($json, true)['data'];

// Insert categories
foreach ($data['categories'] as $category) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO categories (name) VALUES (:name)");
    $stmt->execute(['name' => $category['name']]);
}

// Insert products and attributes
foreach ($data['products'] as $product) {
    $stmt = $pdo->prepare("INSERT IGNORE INTO products (id, name, category, description, in_stock, brand) VALUES (:id, :name, :category, :description, :in_stock, :brand)");
    $stmt->execute([
        'id' => $product['id'],
        'name' => $product['name'],
        'category' => $product['category'],
        'description' => $product['description'],
        'in_stock' => $product['inStock'] ? 1 : 0,
        'brand' => $product['brand']
    ]);
    // Insert galleries
    foreach ($product['gallery'] as $img) {
        $img = trim($img, " `");
        $stmt = $pdo->prepare("INSERT INTO product_gallery (product_id, image_url) VALUES (:product_id, :image_url)");
        $stmt->execute(['product_id' => $product['id'], 'image_url' => $img]);
    }
    // Insert attributes
    foreach ($product['attributes'] as $attr) {
        $stmt = $pdo->prepare("INSERT INTO attributes (product_id, attr_id, name, type) VALUES (:product_id, :attr_id, :name, :type)");
        $stmt->execute([
            'product_id' => $product['id'],
            'attr_id' => $attr['id'],
            'name' => $attr['name'],
            'type' => $attr['type']
        ]);
        foreach ($attr['items'] as $item) {
            $stmt = $pdo->prepare("INSERT INTO attribute_items (product_id, attr_id, item_id, display_value, value) VALUES (:product_id, :attr_id, :item_id, :display_value, :value)");
            $stmt->execute([
                'product_id' => $product['id'],
                'attr_id' => $attr['id'],
                'item_id' => $item['id'],
                'display_value' => $item['displayValue'],
                'value' => $item['value']
            ]);
        }
    }
    // Insert prices
    foreach ($product['prices'] as $price) {
        $stmt = $pdo->prepare("INSERT INTO prices (product_id, amount, currency_label, currency_symbol) VALUES (:product_id, :amount, :currency_label, :currency_symbol)");
        $stmt->execute([
            'product_id' => $product['id'],
            'amount' => $price['amount'],
            'currency_label' => $price['currency']['label'],
            'currency_symbol' => $price['currency']['symbol']
        ]);
    }
}
echo "Import complete.\n";