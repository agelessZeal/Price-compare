<?php

namespace Vanguard\Repositories\RelatedProduct;

use Vanguard\RelatedProduct;

interface RelatedProductRepository
{
    public function getAll();

    /**
     * @param $perPage
     * @param null $search
     * @param null $parent
     * @return mixed
     */
    public function paginate($perPage, $search = null, $parent = null);

    /**
     * Find RelatedProduct by its id.
     *
     * @param $id
     * @return null|RelatedProduct
     */
    public function find($id);

    /**
     * Create new RelatedProduct.
     *
     * @param array $data
     * @return mixed
     */
    public function create(array $data);

    /**
     * Update user specified by it's id.
     *
     * @param $id
     * @param array $data
     * @return RelatedProduct
     */
    public function update($id, array $data);

    /**
     * Delete user with provided id.
     *
     * @param $id
     * @return mixed
     */
    public function delete($id);

    /**
     * Number of RelatedProduct in database.
     *
     * @return mixed
     */
    public function count();

    public function getProductBySku($sku);

    public function findMostPopularProducts();

    public function findRecentSearch();

}