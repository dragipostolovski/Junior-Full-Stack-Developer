<?php
namespace App\Model;

class AttributeItem {
    private $id;
    private $displayValue;
    private $value;

    public function __construct($id, $displayValue, $value) {
        $this->id = $id;
        $this->displayValue = $displayValue;
        $this->value = $value;
    }

    public function getId() { return $this->id; }
    public function getDisplayValue() { return $this->displayValue; }
    public function getValue() { return $this->value; }
}