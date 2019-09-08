@extends('layouts.app')

@section('page-title', trans('app.add_product'))

@section('content')

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                @lang('app.create_new_product')
                <small>@lang('app.product_details')</small>
                <div class="pull-right">
                    <ol class="breadcrumb">
                        <li><a href="{{ route('dashboard') }}">@lang('app.home')</a></li>
                        <li><a href="{{ route('user.list') }}">@lang('app.users')</a></li>
                        <li class="active">@lang('app.create')</li>
                    </ol>
                </div>
            </h1>
        </div>
    </div>

    @include('partials.messages')

    {!! Form::open(['route' => 'product.store', 'files' => true, 'id' => 'product-add-form']) !!}
    <div class="row">
        <div class="col-md-8">
            @include('products.backend.partials.details', ['edit' => false])
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <button type="submit" class="btn btn-primary">
                <i class="fa fa-save"></i>
                @lang('app.create_product')
            </button>
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
    {!! JsValidator::formRequest('Vanguard\Http\Requests\Product\AddProductRequest', '#product-add-form') !!}
@stop