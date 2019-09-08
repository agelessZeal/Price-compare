<?php

namespace Vanguard\Repositories\RelatedProduct;

use Vanguard\RelatedProduct;
use Vanguard\Favorite;
use DB;

class EloquentRelatedProduct implements RelatedProductRepository
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
        return RelatedProduct::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return RelatedProduct::create($data);
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
        $RelatedProduct = $this->find($id);

        $RelatedProduct->update($data);

        return $RelatedProduct;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $RelatedProduct = $this->find($id);

        return $RelatedProduct->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return RelatedProduct::count();
    }

    public function getProductBySku($sku)
    {
        // TODO: Implement getProductBySku() method.
        $query = RelatedProduct::query();
        $query->where('pdt_sku',$sku);
        $result = $query->get();
        return $result;
    }

    public function findMostPopularProducts()
    {
        // TODO: Implement findMostPopularProducts() method.
        $query = RelatedProduct::query();
//        $query->select('*',DB::raw('count(*) as total'));
//        $query->groupBy('pdt_sku');
        $query->where('view_count', '!=' , 0)->orWhereNull('view_count');
        $query->orderBy('view_count','DESC');
        $query->limit(5);
        $result = $query->get();
        return $result;
    }

    public function findRecentSearch()
    {
        // TODO: Implement findRecentSearch() method.
        $query = RelatedProduct::query();
        $query->orderBy('updated_at','DESC');
        $query->where('view_count', '!=' , 0)->orWhereNull('view_count');
        $query->limit(5);
        $result = $query->get();
        return $result;
    }
}
