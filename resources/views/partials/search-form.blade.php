<div class="row" id="search-form-controls">
    @if ($isProfile)
        <div class="col-md-12">
            <h4 style="float: left">@lang('app.my_items')</h4>
            <a class="add-product" href="{{route('product.create')}}">@lang('app.add_new_item')</a>
        </div>
    @else
        <div class="col-md-4 col-sm-4 col-xs-12">
            @if ($isSearch)
                <h4 class="category-item-title" data-keyword="{{$cur_category['keyword']}}" style="float: left">
                    @if ($cur_category['svg_icon']!="")
                        <svg-icon><src href="{{ url('assets/img/svg/'.$cur_category['svg_icon'].'.svg')}}"/></svg-icon>
                    @endif
                    {{ $cur_category['category']}}
                </h4>
            @else
                <h4 style="float: left">{{ $isProfile? trans('app.my_items'):trans('app.favorite')}}</h4>
            @endif
        </div>
        <div class="col-md-8 col-sm-8 col-xs-12">
            <div class="product-order-btns-wrap">
                @if ($isFav)
                    <a class="product-order-btn" id="fav-remove-all-btn">@lang('app.remove_all')</a>
                    <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                @endif
                <a class="product-order-btn" id="higher-price-btn">@lang('app.higher_price')</a>
                <span>&nbsp;&nbsp;|&nbsp;&nbsp;</span>
                <a class="product-order-btn" id="lower-price-btn">@lang('app.lower_price')</a>
                <span>&nbsp;&nbsp;|</span>
                <div class="dropdown">
                    <a class="product-order-btn" id="price-range-drop-btn">@lang('app.price_range')</a>
                    <div id="range-search-form" class="dropdown-content">
                        <label>
                            @lang('app.from')
                            <input type="number" class="form-control" name="range-from" value="0">
                        </label>
                        <label>@lang('app.to')
                            <input type="number" class="form-control" name="range-to">
                            <a id="price-range-btn">@lang('app.upper_ok')</a>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>