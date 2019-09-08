@if ($isSearch)

@else
    <div class="col-2x-grid col-sm-4 col-xs-6 {{ $isSearch? "product-sub-page sub-page-".ceil($pdtOrder/25):"" }}">
        <form action="{{route('product.view.detail')}}" method="GET" id="product-view-form" target="_blank">
            <div class="product-item card"
                 data-title = "{{$product->pdt_title}}"
                 data-image = "{{$product->pdt_imgurl}}"
                 data-price = "{{ number_format((float)$product->pdt_price, 2, '.', '') }}"
                 data-desc = "{{$product->pdt_description}}"
                 data-category = "{{$product->pdt_category_name}}"
                 data-link = "{{$product->pdt_link}}"
                 data-sku = "{{$product->pdt_sku}}">

                <img {{ $isSearch? "data-image=".url($product->pdt_imgurl):"src=".url($product->pdt_imgurl) }}
                     onclick="openViewDetail('{{ $product->pdt_sku }}')">

                <div class="product-item-name">
                    {{ $product->pdt_title }}
                </div>

                <p><span>{{  number_format((float)$product->pdt_price, 2, '.', '') }}</span> USD</p>
                <div class="product-compare-btns">
                    <a class="btn btn-default" onclick="compareViewProduct(this)">
                        <span class="glyphicon glyphicon-heart fav-inactive"></span>
                    </a>
                </div>
            </div>
        </form>
    </div>
@endif