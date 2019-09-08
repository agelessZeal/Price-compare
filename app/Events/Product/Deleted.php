<?php

namespace Vanguard\Events\Product;

use Vanguard\Product;

class Deleted
{
    /**
     * @var Product
     */
    protected $deletedProduct;

    public function __construct(Product $deletedProduct)
    {
        $this->deletedProduct = $deletedProduct;
    }

    /**
     * @return Product
     */
    public function getDeletedProduct()
    {
        return $this->deletedProduct;
    }
}
