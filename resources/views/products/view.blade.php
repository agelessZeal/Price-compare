
<?php
$cur_sku = Input::get('cur_sku');
?>

@extends('layouts.frontend')

@section('page-title', trans('app.product_details'))

@section('product-title', $products[$cur_sku]->pdt_title)
@section('product-description', $products[$cur_sku]->pdt_description)
@section('product-keywords',$products[$cur_sku]->pdt_category_name)

@section('content')


<div class="post-review-section">
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-sm-12 col-xs-12">
                <div class="product-image">
                    <img src="<?= url($products[$cur_sku]->pdt_imgurl);?>" alt="product image">
                </div>
            </div>
            <div class="col-md-6 col-sm-12 col-xs-12 product-info" >
                <h3><?=  $products[$cur_sku]->pdt_title ?></h3>
                <p id="product-price">{{  number_format((float)$products[$cur_sku]->pdt_price, 2, '.', '')}} USD</p>
                <div id="product-description"><?= htmlspecialchars_decode($products[$cur_sku]->pdt_description) ?></div>
                <div class="row"
                     data-title = "{{$products[$cur_sku]->pdt_title}}"
                     data-image = "{{$products[$cur_sku]->pdt_imgurl}}"
                     data-price = "{{  number_format((float)$products[$cur_sku]->pdt_price, 2, '.', '')}}"
                     data-desc = "{{$products[$cur_sku]->pdt_description}}"
                     data-category = "{{$products[$cur_sku]->pdt_category}}"
                     data-link = "{{$products[$cur_sku]->pdt_link}}"
                     data-sku = "{{$products[$cur_sku]->pdt_sku}}"
                     data-related = {{$products[$cur_sku]->pdt_sku_group}}
                >
                    <div class="col-md-12">
                        <div class="product-info-btns">
                             {{--This part will be added by javascript--}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="display: none">
            <div class="col-md-12">
                <h3>Review</h3>
            </div>
            <div class="col-md-2 col-sm-2 col-xs-2">
                <label class="control-label">Rating</label>
            </div>
            <input type="text" id="review-rating" class="ff-rating" />
            <div class="col-md-12">
                <textarea name="review-description" class="form-control" placeholder="Message(required)"></textarea>
                <span class="review-description-note"><span id="descr_count">1500</span>  characters left</span>
            </div>
            <div class="col-md-3 col-sm-4 col-xs-12">
                <button class="btn btn-success btn-lg" id="submit-review">Submit review</button>
            </div>
        </div>
    </div>
</div>
<?php
    $cur_pdt_link = $products[$cur_sku]->pdt_link;
    unset($products[$cur_sku]);
?>
<div class="most-similar-section">
    <div class="container">
        <h3>@lang('app.similar')</h3>
        <div class="row">
            @if (count(array_keys($products))>0)
                @foreach ($products as $sku=>$product)
                    <div class="col-2x-grid col-sm-4 col-xs-6 ">
                        <div class="product-item card"
                             data-title="{{ $product->pdt_title }}"
                             data-image="{{ $product->pdt_imgurl }}"
                             data-price="{{ number_format((float)$product->pdt_price, 2, '.', '') }}"
                             data-desc="{{ $product->pdt_description }}"
                             data-category="{{ $product->pdt_category_name }}"
                             data-link="{{ $product->pdt_link }}"
                             data-sku="{{ $product->pdt_sku }}">
                            <input hidden name="cur_sku" value="{{ $product->pdt_sku }}">
                            <img src="<?= url($product->pdt_imgurl);?>" onclick="openViewDetail('{{ $product->pdt_sku }}')">
                            <div class="product-item-name">
                                {{ $product->pdt_title }}
                            </div>
                            <p><span>{{ number_format((float)$product->pdt_price, 2, '.', '') }}</span> USD</p>
                            <div class="product-compare-btns">
                                <a class="btn btn-default" onclick="compareViewProduct(this)">
                                    <span class="glyphicon glyphicon-heart fav-inactive"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <h3 class="no-product-fond">No product found</h3>
            @endif
        </div>
    </div>
</div>


@stop

@section('scripts')
<script>

    //$('#review-rating').ffrating({isStar:true});

var goToShopPageStr = "@lang('app.go_to_shop_page')";
var removeFromFavoriteStr = "@lang('app.remove_from_favorites')";
var addToFavoriteStr = " @lang('app.add_to_favorites')";

var cur_sku = "{{ $cur_sku }}";
var cur_pdt_link = "{{ $cur_pdt_link }}";

</script>
{!! HTML::script('assets/js/view-detail-manage.js') !!}
@stop