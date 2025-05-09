<?php
namespace App\Database;

use PDO;

class Database {
    private $pdo;

    public function __construct($host, $dbname, $user, $pass) {
        $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
        $this->pdo = new PDO($dsn, $user, $pass);
    }

    public function getConnection() {
        return $this->pdo;
    }
}