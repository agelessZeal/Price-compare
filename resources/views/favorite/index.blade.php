@extends('layouts.frontend')

@section('page-title', trans('app.favorite'))
@section('product-title', 'Best Price Finder')
@section('product-description', 'Favorite page | Best Price Finder')
@section('product-keywords','')

@section('content')

<div class="search-form-section">
<div class="container">
    @include('partials.search-form',[$isSearch=false, $isProfile=false, $isFav=true])
</div>
</div>

<div class="search-products-section">
<div class="container">
    <div class="row"></div>
</div>
</div>


@stop

@section('scripts')
<script>
    var pageCount = 0;
    curPageName = "favorite";
</script>
{!! HTML::script('assets/js/search-manage.js') !!}
@stop