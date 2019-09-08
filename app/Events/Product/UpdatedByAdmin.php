<?php

namespace Vanguard\Events\Product;

use Vanguard\Product;
use Vanguard\User;

class UpdatedByAdmin
{
    /**
     * @var Product
     */
    protected $updatedProduct;

    public function __construct(Product $updatedProduct)
    {
        $this->updatedUser = $updatedProduct;
    }

    /**
     * @return Product
     */
    public function getUpdatedProduct()
    {
        return $this->updatedProduct;
    }
}
