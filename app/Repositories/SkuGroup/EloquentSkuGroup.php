<?php

namespace Vanguard\Repositories\SkuGroup;

use Vanguard\SkuGroup;
use DB;

class EloquentSkuGroup implements SkuGroupRepository
{

    public function __construct()
    {

    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return SkuGroup::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return SkuGroup::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $parent = null)
    {
       
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $skugroup = $this->find($id);

        return $skugroup->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return skugroup::count();
    }

    public function findSkuGroupBySku($sku)
    {
        // TODO: Implement findSkuGroupBySku() method.

        $query = SkuGroup::query();
        $query->where('sku_group','like', "%{$sku}%");
        $result = $query->limit(1)->get();
        return $result;
    }
}
