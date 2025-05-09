<?php
namespace App\Model;

class Product extends AbstractProduct {
    public function getType() {
        return 'product';
    }
}