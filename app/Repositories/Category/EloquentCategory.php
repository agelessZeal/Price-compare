<?php

namespace Vanguard\Repositories\Category;

use Vanguard\Category;
use Vanguard\ParentCategory;
use DB;

class EloquentCategory implements CategoryRepository
{

    public function __construct()
    {

    }

    public function getAll()
    {
        // TODO: Implement getAll() method.
        $query = DB::table('categories');
        $query->join('parent_categories', 'parent_categories.id', '=', 'parent_category_id');
        $query->orderBy('parent_category_id');
        $query->orderBy('categories.id');
        $result = $query->get();
        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function find($id)
    {
        return Category::find($id);
    }


    public function whereFind($id)
    {
        // TODO: Implement whereFind() method.
        $query = DB::table('categories');
        $query->join('parent_categories', 'parent_category_id', '=', 'parent_categories.id');
        $query->where('categories.id', $id);
        $query->select('categories.*','parent_category_name');
        return $query->get()[0];
    }

    /**
     * {@inheritdoc}
     */
    public function create(array $data)
    {
        return Category::create($data);
    }

    /**
     * {@inheritdoc}
     */
    public function paginate($perPage, $search = null, $parent = null)
    {
        $query = DB::table('categories');
        $query->select('*', 'categories.id as category_id');
        $query->join('parent_categories', 'parent_categories.id', '=', 'parent_category_id');
        $query->orderBy('parent_category_id');
        $query->orderBy('categories.id');
        if ($parent) {
            $query->where('parent_category_name', $parent);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('categories.id', "like", "%{$search}%");
                $q->orWhere('category_name', 'like', "%{$search}%");
                $q->orWhere('keyword', 'like', "%{$search}%");
                $q->orWhere('parent_category_name', 'like', "%{$search}%");
                $q->orWhere('parent_keyword', 'like', "%{$search}%");
            });
        }

        $result = $query->orderBy('parent_category_id', 'asc')
            ->paginate($perPage);

        if ($search) {
            $result->appends(['search' => $search]);
        }
        if ($parent) {
            $result->appends(['parent' => $parent]);
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
        $query = Category::query();
        $query->where('id', $id);
        $query->delete();
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return Category::count();
    }

    /**
     * {@inheritdoc}
     */
    public function latest($count = 20)
    {
        return Category::orderBy('created_at', 'DESC')
            ->limit($count)
            ->get();
    }

    public function reFormatCategory()
    {
        $ret = array();
        $categories = $this->getAll();
        $curPrentCategory = "";
        foreach ($categories as $category):
            $tmpArr = array(
                'sub' => $category->category_name,
                'parent' => $category->parent_category_name,
                'svg' => $category->svg_icon,
                'sub_keyword' => $category->keyword,
                'parent_keyword' => $category->parent_keyword,
            );
            if ($curPrentCategory != $category->parent_category_name) {
                $ret[$category->parent_category_name] = array($tmpArr);
            } else {
                array_push($ret[$curPrentCategory], $tmpArr);
            }
            $curPrentCategory = $category->parent_category_name;
        endforeach;

        return $ret;
    }

    public function getParentCategory()
    {
        // TODO: Implement getParentCategory() method.
        $query = ParentCategory::query();
        $query->orderBy('id');
        $result = $query->get();
        return $result;
    }

    public function getCategoryByKeyword($keyword)
    {
        // TODO: Implement getSVGByKeyword() method.
        $categoryInfo = array(
            'category'=>trans('app.all_categories'),
            'keyword'=>'',
            'svg_icon'=>""
        );
        $query = DB::table('categories');
        $query->select('*', 'categories.id as category_id');
        $query->join('parent_categories', 'parent_categories.id', '=', 'parent_category_id');
        $query->where('keyword', $keyword);
        $resultSub = $query->get();

        $query = ParentCategory::query();
        $query->where('parent_keyword', $keyword);
        $resultParent = $query->get();

        if(count($resultParent)>0){
            $categoryInfo['svg_icon'] = $resultParent[0]->svg_icon;
            $categoryInfo['category'] = $resultParent[0]->parent_category_name;
            $categoryInfo['keyword'] = $resultParent[0]->parent_keyword;
        }
        if (count($resultSub)>0){
            $categoryInfo['svg_icon'] = $resultSub[0]->svg_icon;
            $categoryInfo['category'] = $resultSub[0]->parent_category_name;
            $categoryInfo['keyword'] = $resultSub[0]->keyword;
        }
        return $categoryInfo;

    }

}
