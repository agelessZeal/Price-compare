<?php

namespace Vanguard\Http\Controllers\Web;

use function GuzzleHttp\Psr7\str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rules\In;
use Vanguard\Events\Product\Created;
use Vanguard\Events\Product\Deleted;
use Vanguard\Events\Product\UpdatedByAdmin;
use Input;
use Auth;
use Session;
use Vanguard\Http\Controllers\Controller;
use Vanguard\Http\Requests\Product\EditProductRequest;
use Vanguard\Http\Requests\Request;
use Vanguard\Http\Requests\Product\AddProductRequest;
use Vanguard\Product;
use Vanguard\Repositories\Category\CategoryRepository;
use Vanguard\Repositories\Product\ProductRepository;
use Vanguard\Repositories\SkuGroup\SkuGroupRepository;
use Vanguard\Repositories\RelatedProduct\RelatedProductRepository;
use Vanguard\Repositories\Favorite\FavoriteRepository;
use Vanguard\Support\Enum\ProductStatus;
use Vanguard\Support\CJ\CJSDK;
use CROSCON\CommissionJunction\Client;
use Vanguard\Support\MyXMLParser\MyXMLParser;


class ProductController extends Controller
{

    private $category;
    private $product;
    private $skuGroup;
    private $relatedProduct;
    private $favorite;

    public function __construct(CategoryRepository $categoryRepository,
                                ProductRepository $productRepository,
                                SkuGroupRepository $skuGroupRepository,
                                FavoriteRepository $favoriteRepository,
                                RelatedProductRepository $relatedProductRepository)
    {
        //$this->middleware('auth');
        $this->category = $categoryRepository;
        $this->product = $productRepository;
        $this->skuGroup = $skuGroupRepository;
        $this->relatedProduct = $relatedProductRepository;
        $this->favorite = $favoriteRepository;
    }

    public function index()
    {
        $products = $this->product->paginate(
            $perPage = 20,
            Input::get('search'),
            Input::get('category')
        );
        if (Auth::check() && Auth::user()->hasRole('Admin')) {
            return view('products.backend.index', compact('products'));
        } else {
            return view('errors.404');
        }
    }

    public function create()
    {
        $statuses = ProductStatus::lists();
        if (Auth::check()) {
            if (Auth::user()->hasRole('Admin')) {
                return view('products.backend.add', compact('statuses'));
            } else {
                return view('products.add');
            }
        } else {
            return view('errors.404');
        }
    }

    public function store(AddProductRequest $request)
    {
        if (Auth::check()) {

            $userID = Auth::user()->present()->id;
            $pdtImgPath = $request->get('product_image_path');
            $pdtCategory = $request->get('category');
            $pdtTitle = $request->get('title');
            $pdtDescription = $request->get('description');
            $pdtLink = $request->get('link');
            $pdtPrice = $request->get('price');
            $pdtStatus = $request->get('status');
            $pdtRec = array(
                'pdt_category_name' => $pdtCategory,
                'pdt_title' => $pdtTitle,
                'pdt_description' => $pdtDescription,
                'pdt_link' => $pdtLink,
                'pdt_imgurl' => $pdtImgPath,
                'pdt_price' => $pdtPrice,
                'pdt_status' => ProductStatus::BANNED,
                'pdt_sku' => $this->generateRandomString(),
                'user_id'=>$userID
            );
            if (Auth::user()->hasRole('Admin')) {
                $pdtRec['pdt_status'] = $pdtStatus;
            }
            ///Make product Repository
            \Log::info('Uploaded Product=>'.var_export($pdtRec,true));
            $this->product->create($pdtRec);

            if (Auth::user()->hasRole('Admin')) {
                return redirect()->route('product.index')
                    ->withSuccess(trans('app.product_created'));

            } else {
                return redirect()->back()
                    ->withSuccess(trans('app.product_created'));
            }
        } else {
            return view('errors.404');
        }
    }

    public function edit(Product $product)
    {
        $edit = true;
        $statuses = ProductStatus::lists();
        if (Auth::check() && Auth::user()->hasRole('Admin')) {
            return view(
                'products.backend.edit',
                compact('edit', 'product', 'statuses')
            );
        } else {
            return view('errors.404');
        }
    }

    public function delete(Product $product)
    {
        if (Auth::check() && Auth::user()->hasRole('Admin')) {

            $pdtRecInfo = $this->product->find($product->id);
            $pdtSKU = $pdtRecInfo->pdt_sku;
            $relatedPdtInfo = $this->relatedProduct->getProductBySku($pdtSKU);

            if(count($relatedPdtInfo)>0){
                $this->relatedProduct->delete($relatedPdtInfo[0]->id);
            }

            $this->product->delete($product->id);
//          event(new Deleted($product));
            return redirect()->route('product.index')
                ->withSuccess(trans('app.product_deleted'));
        } else {
            return view('errors.404');
        }
    }
    public function viewDetail()
    {
        $sku = Input::get('cur_sku');
        $pdtInProductTbl = $this->product->findBySKU($sku);
        $productInfo = $this->relatedProduct->getProductBySku($sku);

        if(count($productInfo)>0){
            $skuStr = $productInfo[0]->sku_group;
            //Update View Count...
            $viewCount = $productInfo[0]->view_count;
            $this->relatedProduct->update($productInfo[0]->id, array('view_count'=>($viewCount+1)));

            \Log::info('-----------sku group is:'.var_export($skuStr, true));
            $skuArr = explode(",", $skuStr);
            \Log::info('origin sku group is:'.var_export($skuArr, true));

            $products = array();
            if(count($skuArr)>0 && count($skuArr)>6){
                $skuArr = array_diff($skuArr, array($sku));
                $skuArr = array_intersect_key( $skuArr, array_flip( array_rand( $skuArr, 5 ) ) );
                $skuArr = array_values($skuArr); ///Array Re Arrange
                $skuArr[5] = $sku;
            }
            if(count($skuArr)>0){
                foreach ($skuArr as $skuItem):
                    if($skuItem!=""){
                        $relatedPdtInfo = $this->relatedProduct->getProductBySku($skuItem);
                        if(count($relatedPdtInfo)>0){
                            $statusInfo = $relatedPdtInfo[0]->pdt_status;
                            if($statusInfo=='Active'){
                                $products[$skuItem] = $relatedPdtInfo[0];
                            }
                        }
                    }
                endforeach;
            }
            return view('products.view',compact('products'));
        }elseif (count($pdtInProductTbl)>0){
            $keyword = $pdtInProductTbl[0]->pdt_category_name;
            $similarProducts = $this->product->where($keyword, $keyword);
            $products = array($sku=>$pdtInProductTbl[0]);
            $itemCount = 1;
            foreach ($similarProducts as $product):
              if($product->pdt_sku!=$sku){
                  $products[$product->pdt_sku] = $product;
                  $itemCount++;
                  if($itemCount>6) break;
              }
            endforeach;
            //we will save this product into related product table and favorites tables too
                //2.Save Current Product;
                $currentProduct = $pdtInProductTbl[0];
                $pdtRec = array(
                    'pdt_sku' => $sku,
                    'pdt_title' => $currentProduct->pdt_title,
                    'pdt_description' =>$currentProduct->pdt_description,
                    'pdt_imgurl' => $currentProduct->pdt_imgurl,
                    'pdt_category_name' => $currentProduct->pdt_category_name,
                    'pdt_link' => $currentProduct->pdt_link,
                    'pdt_price' => $currentProduct->pdt_price,
                    'sku_group' => $sku,
                    'view_count'=> 1
                );
                $this->relatedProduct->create($pdtRec);
            //we will save this product into related product table and favorites tables too
            return view('products.view',compact('products'));
        }else{
            return view('errors.404');
        }

    }

    public function isValidProductXML($product, $keyword)
    {
        //First Check Image Validation
        $invalidImages = array(
            "p0_v1.jpg",
            "p0_v2.jpg",
            "ZKAIyMrw_.JPG",
        );
        $keyword = trim($keyword);
        $keywords = explode(' ', $keyword);
        foreach ($invalidImages as $invalidImage):
            if (strpos($product->{'image-url'}, $invalidImage) !== false) {
                return false;
            }
        endforeach;
        if (trim($product->{'image-url'}) == "") {
            return false;
        }
        $category = trim($product->{'advertiser-category'});
        if ($category == "") {
            return false;
        }
        if (strpos($category, 'Undefined')) {
            return false;
        }
        $desc = strtolower($product->{'description'});
        $name = strtolower($product->{'name'});
        $category = strtolower($category);

        foreach ($keywords as $item):
            $item = strtolower($item);
            if (strpos($category, $item) === false &&
                strpos($desc, $item) === false &&
                strpos($name, $item) === false
            ) {
                return false;
            }
        endforeach;
        return true;
    }

    public function isValidProductXMLNew($product, $keyword)
    {
        //First Check Image Validation
        $invalidImages = array(
            "p0_v1.jpg",
            "p0_v2.jpg",
            "ZKAIyMrw_.JPG",
        );
        $keyword = trim($keyword);
        $keywords = explode(' ', $keyword);
        foreach ($invalidImages as $invalidImage):
            if (strpos($product[8]['image-url'], $invalidImage) !== false) {
                return false;
            }
        endforeach;
        if (trim($product[8]['image-url']) == "") {
            return false;
        }
        $category = trim($product[3]['advertiser-category']);
        if ($category == "") {
            return false;
        }
        if (strpos($category, 'Undefined')) {
            return false;
        }
        $desc = strtolower($product[7]['description']);
        $name = strtolower($product[13]['name']);
        $category = strtolower($category);

        if($keyword!==""){
            foreach ($keywords as $item):
                $item = strtolower($item);
                if($item!=""){
                    if (strpos($category, $item) === false &&
                        strpos($desc, $item) === false &&
                        strpos($name, $item) === false
                    ) {
                        return false;
                    }
                }
            endforeach;
        }
        return true;
    }

    public function isValidProduct($product, $keyword)
    {
        //First Check Image Validation
        $invalidImages = array(
            "p0_v1.jpg",
            "p0_v2.jpg",
            "ZKAIyMrw_.JPG",
        );

        $keyword = trim($keyword);
        $keywords = explode(' ', $keyword);

        if (count($product) == 19) {

            $category = $product['advertiser-category'];
            if (gettype($category) == 'array') {
                $category = implode('>', $category);
            }
            if ($category == "" || is_numeric($category)) {
                return false;
            }
            //\Log::info('<----Keyword Validation---------->');
            foreach ($keywords as $item):
                if (strpos(strtolower($category), strtolower($item)) === false &&
                    strpos(strtolower($product['description']), strtolower($item)) === false &&
                    strpos(strtolower($product['name']), strtolower($item)) === false
                ) {
                    return false;
                }
            endforeach;

            $imageURL = $product['image-url'];
            if (gettype($imageURL) == 'array') {
                return false;
            }
            if (trim($imageURL) == "") {
                return false;
            }
            foreach ($invalidImages as $invalidImage):
                if (strpos($imageURL, $invalidImage) !== false) {
                    return false;
                }
            endforeach;

            return true;

        } else {
            return false;
        }
    }

    public function parserCJResp($cjArr, $keyword)
    {
        //Get Attribute
        $ret = array();
        $attr = $cjArr['products']['@attributes'];
        \Log::info('Attribute is field is' . var_export($attr, true));
        if (array_key_exists('product', $cjArr['products'])) {
            $products = $cjArr['products']['product'];
            foreach ($products as $product):
                if ($this->isValidProduct($product, $keyword)) {
                    $tmpObj = new \stdClass();
                    $tmpObj->pdt_title = $product['name'];
                    $tmpObj->pdt_imgurl = $product['image-url'];
                    $category = $product['advertiser-category'];
                    \Log::info('---------------' . gettype($product['advertiser-category']));
                    if (gettype($product['advertiser-category']) == 'array') {
                        \Log::info('Category field is Array----------' . $tmpObj->pdt_title);
                        $category = implode('>', $product['advertiser-category']);
                    }
                    $tmpObj->pdt_category_name = (string)$category;
                    $tmpObj->pdt_link = $product['buy-url'];
                    $tmpObj->pdt_price = $product['price'];
                    $tmpObj->pdt_description = $product['description'];
                    $tmpObj->pdt_sku = $product['sku'];
                    array_push($ret, $tmpObj);
                }
            endforeach;
        }
        return $ret;
    }

    public function parserCJXMLOLD($xmlStr, $keyword)
    {
        $ret = array();
        $xml = simplexml_load_string($xmlStr,'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml !== false) {
            \Log::info('Attribute is field is' . var_export($xml->products[0]->attributes(), true));
            foreach ($xml->products[0] as $product):
                if ($this->isValidProductXML($product, $keyword)) {
                    $tmpObj = new \stdClass();
                    $tmpObj->pdt_title = $product->{'name'};
                    $tmpObj->pdt_imgurl = $product->{'image-url'};
                    $tmpObj->pdt_category_name = $product->{'advertiser-category'};
                    $tmpObj->pdt_link = $product->{'buy-url'};
                    $tmpObj->pdt_price = $product->{'price'};
                    $tmpObj->pdt_description = $product->{'description'};
                    $tmpObj->pdt_sku = $product->{'sku'};
                    array_push($ret, $tmpObj);
                }
            endforeach;
        }
        return $ret;
    }

    public function parserCJXML($xmlStr, $keyword)
    {
        $ret = array();
        if(strlen($xmlStr)>123){
            $parser = new MyXMLParser($xmlStr);
            $parserData = $parser->data;
            if(array_key_exists('cj-api', $parserData)){
                $cjAPIData = $parserData['cj-api'];
                if(array_key_exists('products', $cjAPIData)){
                    $products = $cjAPIData['products'];
                    foreach ($products as $product):
                        $pdt = $product['product'];
                        if($this->isValidProductXMLNew($pdt, $keyword)){
                            $tmpObj = new \stdClass();
                            $tmpObj->pdt_title = $pdt[13]['name'];
                            $tmpObj->pdt_imgurl = $pdt[8]['image-url'];
                            $tmpObj->pdt_category_name = $pdt[3]['advertiser-category'];
                            $tmpObj->pdt_link = $pdt[4]['buy-url'];
                            $tmpObj->pdt_price = $pdt[14]['price'];
                            //$tmpObj->pdt_description = str_replace('"','&quot;',$pdt[7]['description']);
                            $tmpObj->pdt_description = $pdt[7]['description'];
                            $tmpObj->pdt_sku = $pdt[17]['sku'];
                            array_push($ret, $tmpObj);
                        }
                    endforeach;
                }
            }
        }
        return $ret;
    }

    public function changeFormatKeywords($string, $replace)
    {
        if(strlen($string)==0) return "";
        $string = str_replace(' ', $replace, $string); // Replaces all spaces with hyphens.
        $string = preg_replace('/[^A-Za-z0-9\-]/', ' ', $string); // Removes special chars.
        $string = preg_replace('!\s+!', ' ', $string);
        $string = str_replace(' ', $replace, $string); // Replaces all spaces with hyphens.
        return $string;

    }

    public function changeRecordType($localProducts)
    {
        $ret = array();
        foreach ($localProducts as $product):
            array_push($ret, $product);
        endforeach;
        return $ret;
    }

    public function sendRelatedPdtToMail($products, $search)
    {
        //Find cheapest 4 product from search.
        usort($products, array($this, 'cmp'));
        $cheapPdt = array_slice($products, 0, 4);
        \Log::info('$cheapest product=>'.var_export($cheapPdt,true));

    }

    public static function cmp($a, $b)
    {
        if ($a->pdt_price > $b->pdt_price) return 1;
        return -1;
    }

    public function connectCJ($keyword)
    {
        $cjAPI = new Client(env('CJ_DEV_KEY'));
        $params = array(
            'website-id' => env('CJ_WEBSITE_ID'),
            'advertiser-ids' => env('CJ_ADVERTISER_IDS'),
            'keywords' => $keyword,
            'page-number' => env('CJ_PAGE_NUMBER'),
            'currency' => 'USD',
            'sort-order' => env('CJ_SORT_ORDER'),
            'sort-by' => env('CJ_SORT_BY'),
            'low-sale-price' => env('CJ_LOW_SALE_PRICE'),
            'high-sale-price' => env('CJ_HIGH_SALE_PRICE'),
            'records-per-page' => env('CJ_RECORD_PER_PAGE'),
        );
        $resp = $cjAPI->productSearch($params);
        return $resp;
    }

    public function searchOld()
    {

        $category = trim(Input::get('category'));
        $search = trim(Input::get('keyword'));

        //Search From Local System
        $localProducts = $this->product->where($category, $search);

        $localProducts = $this->changeRecordType($localProducts);

        $keyword = "";
        $cateLen = strlen($category);
        $searchLen = strlen($search);
        \Log::info('Original keyword information is =>'.$search);
        if($cateLen>0&&$searchLen>0){
            $keyword = $this->changeFormatKeywords($category." ".$search, '+');
        }elseif ($cateLen>0&&$searchLen==0){
            $keyword = $this->changeFormatKeywords($category, '+');
        }elseif ($cateLen==0&&$searchLen>0){
            $keyword = $this->changeFormatKeywords($search, '+');
        }else{
            $keyword = "";
        }
        \Log::info('keyword is=>'.$keyword);

        $executionStartTime = microtime(true);

        $resp = $this->connectCJ($keyword);

        $cjProducts = $this->parserCJXML($resp, $category . ' ' . $search);

        if (count($localProducts) > 0) {
            $products = array_merge($localProducts, $cjProducts);
        } else {
            $products = $cjProducts;
        }
        $this->sendRelatedPdtToMail($products, $search);

        $executionEndTime = microtime(true);

        //The result will be in seconds and milliseconds.
        $seconds = $executionEndTime - $executionStartTime;

        //Print it out
        \Log::info("This script took $seconds to execute.") ;

        $cur_category = $this->category->getCategoryByKeyword($category);

        return view('products.search', compact('products', 'cur_category'));

    }

    public function decode_code($code){
        return preg_replace_callback(
            "@\\\(x)?([0-9a-f]{2,3})@",
            function($m){
                return chr($m[1]?hexdec($m[2]):octdec($m[2]));
            },
            $code
        );
    }

    public function search()
    {

        $category = trim(Input::get('category'));
        $search = trim(Input::get('keyword'));

        //Search From Local System
        $localProducts = $this->product->where($category, $search);

        $localProducts = $this->changeRecordType($localProducts);

        $keyword = "";
        $cateLen = strlen($category);
        $searchLen = strlen($search);
        \Log::info('Original keyword information is =>'.$search);
        if($cateLen>0&&$searchLen>0){
            $keyword = $this->changeFormatKeywords($category." ".$search, '+');
        }elseif ($cateLen>0&&$searchLen==0){
            $keyword = $this->changeFormatKeywords($category, '+');
        }elseif ($cateLen==0&&$searchLen>0){
            $keyword = $this->changeFormatKeywords($search, '+');
        }else{
            $keyword = "";
        }
        \Log::info('keyword is=>'.$keyword);

        $executionStartTime = microtime(true);

        $resp = $this->connectCJ($keyword);

        $cjProducts = $this->parserCJXML($resp, $category . ' ' . $search);

        if (count($localProducts) > 0) {
            $products = array_merge($localProducts, $cjProducts);
        } else {
            $products = $cjProducts;
        }

        /*if(count($products)>0){
            //we need to check
            //1. check session variable
            $isPrevSearch = session('is_prev_search');
            if($isPrevSearch==null&&Auth::check()){
                session(['is_prev_search'=>true]);
                \Log::info('We will send first search result into mail account...');
                $this->sendRelatedPdtToMail($products, $search);
            }else{
             //nothing else
            }

        }*/

        $retDATA = "";
        if(count($products)>0){
            foreach ($products as $product):
                $retDATA .= str_replace('\\','',$product->pdt_sku)."****";
                $retDATA .= addslashes($product->pdt_title)."****";
                $descp = addslashes($product->pdt_description);
                $descp = trim(preg_replace('/\s\s+/', '\n', $descp));
                $retDATA .= $descp."****";
                $retDATA .= addslashes($product->pdt_category_name)."****";
                $retDATA .= $product->pdt_price."****";
                $retDATA .= $product->pdt_link."****";
                $retDATA .= $product->pdt_imgurl."****";
            endforeach;
        }

        $products = $retDATA;

        $executionEndTime = microtime(true);

        //The result will be in seconds and milliseconds.
        $seconds = $executionEndTime - $executionStartTime;

        //Print it out
        \Log::info("This script took $seconds to execute.") ;

        $cur_category = $this->category->getCategoryByKeyword($category);

        return view('products.search', compact('products', 'cur_category'));

    }

    public function updateDetail(Product $product, EditProductRequest $request)
    {
        $pdtImgPath = $request->get('product_image_path');
        $pdtCategory = $request->get('category');
        $pdtTitle = $request->get('title');
        $pdtDescription = $request->get('description');
        $pdtLink = $request->get('link');
        $pdtPrice = $request->get('price');
        $pdtStatus = $request->get('status');

        $pdtRec = array(
            'pdt_category_name' => $pdtCategory,
            'pdt_title' => $pdtTitle,
            'pdt_description' => $pdtDescription,
            'pdt_link' => $pdtLink,
            'pdt_price' => $pdtPrice,
            'pdt_status' => $pdtStatus
        );
        if ($pdtImgPath != "") $pdtRec['pdt_imgurl'] = $pdtImgPath;

        $this->product->update($product->id, $pdtRec);
        //If current product exist in favorite table and related product table, must update the product info too.
        $sku = $product->pdt_sku;
        $productInfo = $this->favorite->findBySKU($sku);
        if(count($productInfo)>0){
            $favRecId = $productInfo[0]->id;
            $this->favorite->update($favRecId, $pdtRec);
        }
        $productInfo = $this->relatedProduct->getProductBySku($sku);
        if(count($productInfo)>0){
            $recID = $productInfo[0]->id;
            $this->relatedProduct->update($recID, $pdtRec);
        }
//      event(new UpdatedByAdmin($product));

        return redirect()->back()
            ->withSuccess(trans('app.product_updated'));
    }


    /**
     * This function is used in uploading cropped images...
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadImage()
    {

        $pdtImgPath = "";
        $uploadDir = "upload/products/";
        if (Input::hasFile('pdt-image')) {
            $file = Input::file('pdt-image');
            $fileName = $this->generateRandomString() . '.png';
            $file->move($uploadDir, $fileName);
            $pdtImgPath = $uploadDir . $fileName;
        }

        return response()->json($pdtImgPath, 200);

    }

    public function activeBulkProduct()
    {
        $ret = array(
            'status' => 'fail',
            'data' => ''
        );
        $selItems = Input::get('pdt_infos');
        if (Auth::check() && $selItems != null && count($selItems) > 0) {
            foreach ($selItems as $item):
                $pdtInfoArr = explode('-', $item);
                $productId = $pdtInfoArr[0];
                $productTitle = str_replace($productId . '-', "", $item);
                $this->product->update($productId, array('pdt_status' => ProductStatus::ACTIVE));
            endforeach;
            $ret['status'] = 'success';
            $ret['data'] = 'active operation message';
        }
        return response()->json($ret, 200);
    }

    public function deleteBulkProduct()
    {
        $ret = array(
            'status' => 'fail',
            'data' => ''
        );
        $selItems = Input::get('pdt_infos');
        if (Auth::check() && $selItems != null && count($selItems) > 0) {
            foreach ($selItems as $item):
                $pdtInfoArr = explode('-', $item);
                $productId = $pdtInfoArr[0];
                $productTitle = str_replace($productId . '-', "", $item);

                ///we need to remove all products on the related products table
                $pdtRecInfo = $this->product->find($productId);
                $pdtSKU = $pdtRecInfo->pdt_sku;
                $relatedPdtInfo = $this->relatedProduct->getProductBySku($pdtSKU);
                if(count($relatedPdtInfo)>0){
                    $this->relatedProduct->delete($relatedPdtInfo[0]->id);
                }
                //We will remove favorites tables too
                $favPdtInfo = $this->favorite->findBySKU($pdtSKU);
                if(count($favPdtInfo)>0){
                    $favRecId = $favPdtInfo[0]->id;
                    $this->favorite->delete($favRecId);
                }

                //we delete real product from product table.
                $this->product->delete($productId);
            endforeach;
            $ret['status'] = 'success';
            $ret['data'] = 'delete operation message';
        }

        return response()->json($ret, 200);

    }

    public function storeRelatedProducts()
    {
        $ret = array(
            'status'=>'',
            'data'=>''
        );
        $relatedProducts = Input::get('related_products');
        $pdtTitle = Input::get('title');
        $pdtImage = Input::get('image');
        $pdtCategory = Input::get('category');
        $pdtDescription = Input::get('description');
        $pdtLink = Input::get('link');
        $pdtPrice = Input::get('price');
        $pdtSku = Input::get('sku');
        //1. save SkuGroup
        $pdtGroup = $this->getSkuGroupAndProducts($relatedProducts);
        $skuGroup = array_keys($pdtGroup);
        $skuGroupStr = implode(",",$skuGroup);
        //2.Save Current Product;
        $currentProduct = array(
            'pdt_sku' => $pdtSku,
            'pdt_title' => $pdtTitle,
            'pdt_description' =>$pdtDescription,
            'pdt_imgurl' => $pdtImage,
            'pdt_category_name' => $pdtCategory,
            'pdt_link' => $pdtLink,
            'pdt_price' => $pdtPrice,
            'sku_group' => $pdtSku.','.$skuGroupStr
        );
        $prevProduct = $this->relatedProduct->getProductBySku($pdtSku);

        if(count($prevProduct)!=0){
            //Will update sku group str....
            $skuGroupStr = ($prevProduct[0]->sku_group!="")? $prevProduct[0]->sku_group.','.$skuGroupStr:$skuGroupStr;
            $this->relatedProduct->update($prevProduct[0]->id, array('sku_group'=>$skuGroupStr));
        }else{
            $this->relatedProduct->create($currentProduct);
        }
        //3.Save Related Product;
        foreach ($pdtGroup as $key=>$pdtItem):
            $oldRPdtInfo = $this->relatedProduct->getProductBySku($key);
            if (count($oldRPdtInfo)==0){
                $tempSkuGroupStr = $pdtSku.','.$skuGroupStr;
                $tempSkuArr = explode(',', $tempSkuGroupStr);
                $filteredArr = array_unique($tempSkuArr);
                $newSkuGroup = implode(",",$filteredArr);
                $pdtItem['sku_group'] = $newSkuGroup;
                $this->relatedProduct->create($pdtItem);
            }
        endforeach;
        $ret['status'] = 'success';
        $ret['data'] = $pdtSku;

        return response()->json($ret,200);
    }

    public function generateRandomString($length = 10)
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getSkuGroupAndProducts($relatedProducts)
    {
         $pdtGroup = array();
         if(count($relatedProducts)>0){
             foreach ($relatedProducts as $relatedPdt):
                 $pdtInfo = explode('*****', $relatedPdt);
                 if(count($pdtInfo)>6){
                     $tempArr = array(
                         'pdt_sku' => $pdtInfo[0],
                         'pdt_title' => $pdtInfo[1],
                         'pdt_description' =>$pdtInfo[2],
                         'pdt_imgurl' => $pdtInfo[3],
                         'pdt_category_name' => $pdtInfo[4],
                         'pdt_link' => $pdtInfo[5],
                         'pdt_price' => $pdtInfo[6],
                     );
                     $pdtGroup[$pdtInfo[0]] = $tempArr;
                 }
             endforeach;
         }
         return $pdtGroup;
    }

}