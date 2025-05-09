<?php
namespace App\Model;

class Attribute {
    private $id;
    private $name;
    private $type;
    private $items;

    public function __construct($id, $name, $type, $items) {
        $this->id = $id;
        $this->name = $name;
        $this->type = $type;
        $this->items = $items;
    }

    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getType() { return $this->type; }
    public function getItems() { return $this->items; }
}