<div class="col-2x-grid col-sm-4 col-xs-6 {{ $isSearch? "product-sub-page sub-page-".ceil($pdtOrder/25):"" }}">
    <form action="{{route('product.view.detail')}}" method="POST" id="product-view-form" target="_blank">
        <div class="product-item"
             data-title = "{{$product->pdt_title}}"
             data-image = "{{$product->pdt_imgurl}}"
             data-price = "{{$product->pdt_price}}"
             data-description = "{{$product->pdt_description}}"
             data-category = "{{$product->pdt_category_name}}"
             data-link = "{{$product->pdt_link}}"
             data-sku = "{{$product->pdt_sku}}">

            <input hidden name="_token" value="{{csrf_token()}}">
            <input hidden name="title" value="{{$product->pdt_title}}">
            <input hidden name="description" value="{{$product->pdt_description}}">
            <input hidden name="image" value="{{$product->pdt_imgurl}}">
            <input hidden name="category" value="{{$product->pdt_category_name}}">
            <input hidden name="link" value="{{$product->pdt_link}}">
            <input hidden name="price" value="{{$product->pdt_price}}">
            <input hidden name="sku" value="{{$product->pdt_sku}}">

            <img {{ $isSearch? "data-image=".url($product->pdt_imgurl):"src=".url($product->pdt_imgurl) }} onclick="openProductView(this)">

            <div class="product-item-name">
                {{ $product->pdt_title }}
            </div>
            <p>Price <span>{{ $product->pdt_price }}</span>USD</p>
            <div class="product-compare-btns">
                <a class="btn btn-default" onclick="openProduct(this)">OPEN</a>
                <a class="btn btn-default" onclick="compareProduct(this)">COMPARE</a>
            </div>
        </div>
    </form>
</div>