<?php

namespace Vanguard\Http\Controllers\Web;

use Illuminate\Validation\Rules\In;
use Vanguard\Http\Controllers\Controller;
use Vanguard\RelatedProduct;
use Vanguard\Repositories\Favorite\FavoriteRepository;
use Vanguard\Repositories\Category\CategoryRepository;
use Vanguard\Repositories\Product\ProductRepository;
use Vanguard\Repositories\RelatedProduct\RelatedProductRepository;
use Auth;
use Input;

class FavoriteController extends Controller
{

    private $favorite;
    private $product;
    private $relatedProduct;

    public function __construct(FavoriteRepository $favoriteRepository,
                                RelatedProductRepository $relatedProductRepository,
                                ProductRepository $productRepository)
    {
        $this->favorite = $favoriteRepository;
        $this->product = $productRepository;
        $this->relatedProduct = $relatedProductRepository;
    }
    /**
     * Displays dashboard based on user's role.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $products = array();
        return view('favorite.index',compact('products'));
    }

    public function profile()
    {
        if(Auth::check()){
            $products = array();
            $userID = Auth::user()->present()->id;
            $products = $this->product->findUserProduct($userID);

            $retDATA = "";
            if(count($products)>0){
                foreach ($products as $product):
                    $retDATA .= str_replace('\\','',$product->pdt_sku)."****";
                    $retDATA .= $product->pdt_title."****";
                    $retDATA .= $product->pdt_description."****";
                    $retDATA .= $product->pdt_category_name."****";
                    $retDATA .= $product->pdt_price."****";
                    $retDATA .= $product->pdt_link."****";
                    $retDATA .= $product->pdt_imgurl."****";
                endforeach;
            }

            $products = $retDATA;

            return view('favorite.profile',compact('products'));
        }else{
            return view('errors.404');
        }

    }

    public function addToFavorite()
    {
        $ret = array(
            'status'=>'fail',
            'data' =>''
        );
        if (Auth::check()){
            $pdtTitle = Input::get('title');
            $pdtDescription = Input::get('description');
            $pdtImgPath = Input::get('image');
            $pdtCategory = Input::get('category');
            $pdtLink = Input::get('link');
            $pdtPrice = Input::get('price');
            $pdtSku = Input::get('sku');

            $relatedProducts = Input::get('related_products');
            //we need to get sku group id.

            $pdtRec = array(
                'pdt_category_name' => $pdtCategory,
                'pdt_title' => $pdtTitle,
                'pdt_description' => $pdtDescription,
                'pdt_link' => $pdtLink,
                'pdt_imgurl' => $pdtImgPath,
                'pdt_price' => $pdtPrice,
                'pdt_sku' => $pdtSku,
            );
            //Check previous product
            $prevRec = $this->favorite->findBySKU($pdtSku);
            if(count($prevRec)>0){ //update previous product
                $ret['status'] = 'success';
                $ret['data'] = 'Product has been added already.';
                $this->favorite->update($prevRec[0]->id, $pdtRec);

            }else{
                $this->favorite->create($pdtRec);
                $ret['status'] = 'success';
                $ret['data'] = 'Product has been added in your profile';
            }

        }else{
            $ret['data'] = "Failed to get user information, please login and try again!";
        }
        return response()->json($ret, 200);

    }

    public function deleteProfileProduct()
    {
        $ret= array(
            'status'=>'fail',
            'data'=>''
        );
        if(Auth::check()){

            $sku = Input::get('sku');
            $this->favorite->deleteBySKU($sku);
            //If this product exist in related product table and favorite table, must be deleted in that tables too.
            $productInfo = $this->favorite->findBySKU($sku);
            if(count($productInfo)>0){
                $favRecId = $productInfo[0]->id;
                $this->favorite->delete($favRecId);
            }
            $productInfo = $this->relatedProduct->getProductBySku($sku);
            if(count($productInfo)>0){
                $recID = $productInfo[0]->id;
                $this->relatedProduct->delete($recID);
            }
            //If current product exist on the product table, we should delete the product from product table too.
            $pdtRecInProductTable = $this->product->findBySKU($sku);
            if(count($pdtRecInProductTable)>0&&Auth::check()){
                $this->product->delete($pdtRecInProductTable[0]->id);
            }

            $ret['status'] = 'success';
            $ret['data'] = 'Delete Operation Success';
        }
        return response()->json($ret, 200);
    }
}
