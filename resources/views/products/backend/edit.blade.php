@extends('layouts.app')

@section('page-title', trans('app.edit_product'))

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                {{ $product->pdt_title }}
                <small>@lang('app.edit_product_details')</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li><a href="{{ route('product.index') }}">@lang('app.products')</a></li>
                        <li class="active">@lang('app.edit')</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    @include('partials.messages')

    {!! Form::open(['route' => ['product.update.detail',$product->id], 'method' => 'PUT','files' => true, 'id' => 'product-edit-form']) !!}
    <div class="row">
        <div class="col-md-8">
            @include('products.backend.partials.details', ['edit' => $edit])
        </div>
    </div>
    {!! Form::close() !!}

@stop

@section('styles')
    {!! HTML::style('assets/css/bootstrap-datetimepicker.min.css') !!}
@stop

@section('scripts')
    {!! HTML::script('assets/js/moment.min.js') !!}
    {!! HTML::script('assets/js/bootstrap-datetimepicker.min.js') !!}
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Product\EditProductRequest', '#product-edit-form') !!}
@stop