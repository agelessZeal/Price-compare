<?php

namespace Vanguard\Repositories\Product;

use Vanguard\Product;
use Vanguard\Support\Enum\ProductStatus;

class EloquentProduct implements ProductRepository
{

    public function __construct()
    {

    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        $query = Product::query();
        $query->where('pdt_status',ProductStatus::ACTIVE);
        $result = $query->get();
        return $result;

    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Product::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return Product::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $category = null)
    {
        $query = Product::query();
        if ($category) {
            $query->where('pdt_category_name', $category);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pdt_title', "like", "%{$search}%");
                $q->orWhere('pdt_description', 'like', "%{$search}%");
                $q->orWhere('pdt_price', 'like', "%{$search}%");
                $q->orWhere('pdt_category_name', 'like', "%{$search}%");
            });
        }
        $result = $query->orderBy('id', 'desc')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }
        if ($category) {
            $result->appends(['category' => $category]);
        }
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $product = $this->find($id);

        $product->update($data);

        return $product;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $category = $this->find($id);

        return $category->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return Product::count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return Product::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }

    public function findBySKU($sku)
    {
        // TODO: Implement findBySKU() method.
        $query = Product::query();
        $query->where('pdt_sku',$sku);
        $result = $query->get();
        return $result;
    }

    public function where($category=null, $search=null)
    {
        // TODO: Implement where() method.
        $query = Product::query();
        $query->where('pdt_status',ProductStatus::ACTIVE);
        if($category){
            $query->where('pdt_category_name',$category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pdt_title', "like", "%{$search}%");
//                $q->orWhere('pdt_description', 'like', "%{$search}%");
//                $q->orWhere('pdt_link', 'like', "%{$search}%");
//                $q->orWhere('pdt_price', 'like', "%{$search}%");
//                $q->orWhere('pdt_category_name', 'like', "%{$search}%");
            });
        }
        $result = $query->orderBy('pdt_title')->get();

        return $result;
    }

    public function findUserProduct($userID)
    {
        // TODO: Implement findUserProduct() method.
        $query = Product::query();
        $query->where('user_id', $userID);
        $query->where('pdt_status', ProductStatus::ACTIVE);
        $result = $query->get();
        return $result;

    }

}
