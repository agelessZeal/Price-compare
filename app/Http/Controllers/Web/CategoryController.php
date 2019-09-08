<?php

namespace Vanguard\Http\Controllers\Web;
use Vanguard\Category;
use Input;
use Auth;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Category\EditCategoryRequest;
use Vanguard\Http\Requests\Request;
use Vanguard\Http\Requests\Category\AddCategoryRequest;
use Vanguard\Repositories\Category\CategoryRepository;

class CategoryController extends Controller
{

    private $category;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->middleware('auth');
        $this->category = $categoryRepository;
    }

    public function index()
    {
        $category_list = $this->category->paginate(
            $perPage = 13,
            Input::get('search'),
            Input::get('parent')
        );

        $parent_categories = $this->category->getParentCategory();

        return view('category.index', compact('category_list','parent_categories'));
    }

    public function create()
    {
        $parent_categories = $this->category->getParentCategory();

        return view('category.add', compact('parent_categories'));
    }

    public function store(AddCategoryRequest $request)
    {
        $parent = $request->get('parent');
        $title = $request->get('title');
        $keyword = $request->get('keyword');
        $rec = array(
            'category_name'=>$title,
            'keyword'=>$keyword,
            'parent_category_id'=>$parent
        );
        $this->category->create($rec);
        return redirect()->route('category.index')
            ->withSuccess(trans('app.category_created'));

    }

    public function edit($categoryID)
    {
        $parent_categories = $this->category->getParentCategory();
        $edit = true;
        $category = $this->category->whereFind($categoryID);
        return view(
            'category.edit',
            compact('edit','category','parent_categories')
        );
    }

    public function delete(Category $category)
    {
        $this->category->delete($category->id);
        return redirect()->route('category.index')
            ->withSuccess(trans('app.category_deleted'));

    }

    public function updateDetail($category, EditCategoryRequest $request)
    {
        $parent = $request->get('parent');
        $title = $request->get('title');
        $keyword = $request->get('keyword');
        $rec = array(
            'category_name'=>$title,
            'keyword'=>$keyword,
            'parent_category_id'=>$parent
        );
        $this->category->update($category, $rec);
        return redirect()->back()
            ->withSuccess(trans('app.category_updated'));
    }

}