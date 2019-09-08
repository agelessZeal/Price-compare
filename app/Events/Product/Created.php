<?php

namespace Vanguard\Events\Product;

use Vanguard\Product;

class Created
{
    /**
     * @var Product
     */
    protected $createdProduct;

    public function __construct(Product $createdProduct)
    {
        $this->createdProduct = $createdProduct;
    }

    /**
     * @return Product
     */
    public function getCreatedProduct()
    {
        return $this->createdProduct;
    }
}
