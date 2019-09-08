@extends('layouts.frontend')

@section('page-title', trans('app.home'))
@section('product-title', 'Best Price Finder')
@section('product-description', 'Best Price Finder')
@section('product-keywords', 'Women Clothing,male Clothing,Cell phone mobile,Computer,Consumer Electronics,Jewelry,
                              Home,Luggage Bag,Shoes,kids,sport,Health,Watches,Toys,Wedding,Novelty,Automobile,Lighting
                              ,Furniture,Electronic Supplies,School Supplies,Home Appliances,Home Improvement
                              ,Security protection,Tools,Hair Extensions')


@section('content')

<div class="banner-section">
    <div class="container">
        <img src="{{url('assets/img/banner.png')}}">
    </div>
</div>


<div class="most-popular-section">
    <div class="container">
        <h3>MOST POPULAR</h3>
        <div class="row">
            @if(count($mostPopularPdts))
                @foreach ($mostPopularPdts as $product)
                    @include('partials.product',[$isSearch=false])
                @endforeach
            @else
                <h3 class="no-product-fond">@lang('app.no_products_data')</h3>
            @endif
        </div>
    </div>
</div>
<div class="recent-search-section">
    <div class="container">
        <h3>RECENT SEARCHS</h3>
        <div class="row">
            @if(count($recentSearchPdts))
                @foreach ($recentSearchPdts as $product)
                    @include('partials.product',[$isSearch=false])
                @endforeach
            @else
                <h3 class="no-product-fond">@lang('app.no_products_data')</h3>
            @endif
        </div>
    </div>
</div>

@stop

@section('scripts')



@stop