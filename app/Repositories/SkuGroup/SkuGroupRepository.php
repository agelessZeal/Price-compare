<?php

namespace Vanguard\Repositories\SkuGroup;

use Vanguard\SkuGroup;

interface SkuGroupRepository
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
     * Find SkuGroup by its id.
     *
     * @param $id
     * @return null|SkuGroup
     */
    public function find($id);

    /**
     * Create new SkuGroup.
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
     * @return SkuGroup
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
     * Number of SkuGroup in database.
     *
     * @return mixed
     */
    public function count();

    public function findSkuGroupBySku($sku);

}