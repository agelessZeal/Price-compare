<?php

namespace Vanguard\Repositories\Favorite;

use Vanguard\Favorite;
use DB;

class EloquentFavorite implements FavoriteRepository
{

    public function __construct()
    {

    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        return Favorite::all();
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Favorite::find($id);
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return Favorite::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $category = null)
    {
        $query = Favorite::query();

        if ($category) {
            $query->where('pdt_category_name', $category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pdt_title', "like", "%{$search}%");
                $q->orWhere('pdt_description', 'like', "%{$search}%");
                $q->orWhere('pdt_link', 'like', "%{$search}%");
                $q->orWhere('pdt_price', 'like', "%{$search}%");
                $q->orWhere('pdt_category_name', 'like', "%{$search}%");
            });
        }

        $result = $query->orderBy('id', 'desc')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function update($id, array $data)
    {
        $category = $this->find($id);

        $category->update($data);

        return $category;
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        $category = $this->find($id);

        return $category->delete();
    }

    public function deleteBySKU($sku)
    {
        // TODO: Implement deleteBySKU() method.
        $query  = Favorite::query();
        $query->where('pdt_sku', $sku);
        $query->delete();
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return Favorite::count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return Favorite::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }

    public function findBySKU($sku)
    {
        // TODO: Implement findBySKU() method.
        $query = Favorite::query();
        $query->where('pdt_sku','=',$sku);
        $result = $query->get();
        return $result;
    }

    public function where($category=null, $search=null)
    {
        // TODO: Implement where() method.
        $query = Favorite::query();
        $query->where('pdt_sku','Active');
        if($category){
            $query->where('pdt_category_name',$category);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('pdt_title', "like", "%{$search}%");
                $q->orWhere('pdt_description', 'like', "%{$search}%");
                $q->orWhere('pdt_link', 'like', "%{$search}%");
                $q->orWhere('pdt_price', 'like', "%{$search}%");
                $q->orWhere('pdt_category_name', 'like', "%{$search}%");
            });
        }
        $result = $query->orderBy('pdt_title')->get();
        return $result;
    }

    public function findMostPopularProducts()
    {
        // TODO: Implement findMostPopularProducts() method.
        $query = Favorite::query();
        $query->select('*',DB::raw('count(*) as total'));
        $query->groupBy('pdt_sku');
        $query->orderBy('total','DESC');
        $query->limit(5);
        $result = $query->get();
        return $result;
    }

    public function findRecentSearch()
    {
        // TODO: Implement findRecentSearch() method.
        $query = Favorite::query();
        $query->orderBy('favorites.updated_at','DESC');
        $query->groupBy('pdt_sku');
        $query->limit(5);
        $result = $query->get();
        return $result;
    }
}
