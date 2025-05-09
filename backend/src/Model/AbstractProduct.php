<?php
namespace App\Model;

abstract class AbstractProduct {
    protected $id;
    protected $name;
    protected $category;
    protected $attributes;
    protected $price;
    protected $gallery;
    protected $inStock;

    public function __construct($id, $name, $category, $attributes, $price, $gallery, $inStock) {
        $this->id = $id;
        $this->name = $name;
        $this->category = $category;
        $this->attributes = $attributes;
        $this->price = $price;
        $this->gallery = $gallery;
        $this->inStock = $inStock;
    }

    abstract public function getType();

    // Getters...
    public function getId() { return $this->id; }
    public function getName() { return $this->name; }
    public function getCategory() { return $this->category; }
    public function getAttributes() { return $this->attributes; }
    public function getPrice() { return $this->price; }
    public function getGallery() { return $this->gallery; }
    public function isInStock() { return $this->inStock; }
}