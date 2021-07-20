<?php


namespace App\Entity;


use phpDocumentor\Reflection\Types\Collection;

class Cart
{
    private $products;

    /**
     * @param mixed $products
     * @return Cart
     */
    public function setProducts($products)
    {
        $this->products = $products;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }
}