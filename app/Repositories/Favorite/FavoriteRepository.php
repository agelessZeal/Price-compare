<?php

namespace Vanguard\Repositories\Favorite;

use Vanguard\Favorite;

interface FavoriteRepository
{
    public function getAll();
    /**
     * Paginate registered category.
     *
     * @param $perPage
     * @param null $search
     * @param null $category
     * @return mixed
     */
    public function paginate($perPage, $search = null, $category = null);

    /**
     * Find category by its id.
     *
     * @param $id
     * @return null|Category
     */
    public function find($id);

    /**
     * Create new Category.
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
     * @return Category
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
     * Number of category in database.
     *
     * @return mixed
     */
    public function count();

    /**
     * Get latest {$count} category from database.
     *
     * @param $count
     * @return mixed
     */
    public function latest($count = 20);

    /**
     * @param null $category
     * @param null $search
     * @return mixed
     */
    public function where($category=null, $search=null);


    /**
     * @param $userID
     * @param $sku
     * @return mixed
     */

    public function findBySKU($sku);

    public function findMostPopularProducts();

    public function findRecentSearch();

    public function deleteBySKU($sku);

}