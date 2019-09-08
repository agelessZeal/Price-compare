@extends('layouts.frontend')

@section('page-title', trans('app.profile'))
@section('product-title', 'Best Price Finder')
@section('product-description', 'User Profile page | Best Price Finder')
@section('product-keywords','')

@section('content')

<div class="search-form-section">
    <div class="container">
        @include('partials.search-form',[$isSearch=false,$isProfile=true])
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
        var pageCount = "{{ 10 }}";
        var pageContent = `<?= $products; ?>`;
        curPageName = "profile";
    </script>
    {!! HTML::script('assets/js/search-manage.js') !!}
@stop