@extends('layouts.frontend')

@section('page-title', trans('app.search'))
@section('product-title', 'Search Best Price')
@section('product-description', '')

@section('content')

<div class="search-form-section">
    <div class="container">
        @include('partials.search-form',[$isSearch=true,$isProfile=false, $isFav=false])
    </div>
</div>

<div class="search-products-section">
    <div class="container">
        <div class="row">

        </div>
    </div>
</div>

@stop

@section('scripts')
    <script>
        var pageCount = "{{ 10 }} ";
        var pageContent = "<?= $products; ?>";
        curPageName = 'search';;
    </script>
{!! HTML::script('assets/js/search-manage.js') !!}
@stop